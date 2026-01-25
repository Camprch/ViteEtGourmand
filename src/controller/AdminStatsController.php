<?php
declare(strict_types=1);

// Contrôleur pour la gestion des statistiques par un administrateur.

// Il démontre l'utilisation combinée d'une base SQL (source de vérité) et d'une base NoSQL (MongoDB) pour le stockage et la consultation des stats.

// 1. Calcul des stats via SQL (requête sur les commandes)
// 2. Sauvegarde/"upsert" des stats dans MongoDB (NoSQL)
// 3. Lecture des stats depuis MongoDB pour affichage (preuve d'utilisation NoSQL)

class AdminStatsController
{
    // Connexion PDO à la base SQL
    private PDO $pdo;

    // Constructeur : injection de la connexion PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Vérifie que l'utilisateur courant est admin
    private function requireAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'ADMIN') {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(403, 'Accès refusé', 'Vous n’avez pas les droits nécessaires pour accéder à cette page.');
        }
    }

    // Récupère une variable d'environnement depuis le .env
    private function env(string $key, ?string $default = null): ?string
    {
        $envPath = __DIR__ . '/../../.env';
        if (!file_exists($envPath)) return $default;

        $lines = file($envPath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) return $default;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;
            $firstChar = $line[0] ?? '';
            if ($firstChar === '#' || $firstChar === ';') continue;
            if (!str_contains($line, '=')) continue;

            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            if ($k === $key) {
                $v = trim($v);
                $len = strlen($v);
                if ($len >= 2) {
                    $first = $v[0];
                    $last = $v[$len - 1];
                    if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                        $v = substr($v, 1, -1);
                    }
                }
                return $v;
            }
        }

        return $default;
    }

    // Affiche la page de statistiques (calcul, stockage, lecture)
    public function index(): void
    {
        $this->requireAdmin();

        $dateFrom = $_GET['from'] ?? null; // YYYY-MM-DD
        $dateTo   = $_GET['to'] ?? null;   // YYYY-MM-DD
        $period   = $_GET['period'] ?? 'day'; // day | week | month

        $dateFrom = is_string($dateFrom) ? trim($dateFrom) : null;
        $dateTo = is_string($dateTo) ? trim($dateTo) : null;
        $userProvidedRange = ($dateFrom !== '' && $dateFrom !== null) || ($dateTo !== '' && $dateTo !== null);

        $mongoDsn = $this->env('MONGO_DSN');
        $mongoDb  = $this->env('MONGO_DB', 'vite_gourmand');
        $collection = 'orders_stats';

        $error = null;
        $stats = [];
        $caTotal = 0.0;
        $nbTotal = 0;
        $volumeSeries = [];

        try {
            if (!$mongoDsn) {
                throw new RuntimeException("MONGO_DSN manquant dans .env");
            }
            if (!class_exists(\MongoDB\Driver\Manager::class)) {
                throw new RuntimeException("Extension PHP MongoDB non installée");
            }

            $manager = new \MongoDB\Driver\Manager($mongoDsn);

            $match = [];

            // Si aucune période fournie, on détecte automatiquement min/max pour afficher tout l'historique.
            if (!$userProvidedRange) {
                $pipelineRange = [[
                    '$group' => [
                        '_id' => null,
                        'min_date' => ['$min' => '$accepted_at'],
                        'max_date' => ['$max' => '$accepted_at'],
                    ],
                ]];

                $cmdRange = new \MongoDB\Driver\Command([
                    'aggregate' => $collection,
                    'pipeline' => $pipelineRange,
                    'cursor' => new stdClass(),
                ]);

                $resRange = $manager->executeCommand($mongoDb, $cmdRange)->toArray();
                if ($resRange && !empty($resRange[0]->min_date) && !empty($resRange[0]->max_date)) {
                    $minDt = $resRange[0]->min_date->toDateTime();
                    $maxDt = $resRange[0]->max_date->toDateTime();
                    $dateFrom = $minDt->format('Y-m-d');
                    $dateTo = $maxDt->format('Y-m-d');
                }
            }
            if ($dateFrom || $dateTo) {
                $range = [];
                if ($dateFrom) {
                    $dt = new DateTimeImmutable($dateFrom . ' 00:00:00');
                    $range['$gte'] = new \MongoDB\BSON\UTCDateTime($dt->getTimestamp() * 1000);
                }
                if ($dateTo) {
                    $dt = new DateTimeImmutable($dateTo . ' 23:59:59');
                    $range['$lte'] = new \MongoDB\BSON\UTCDateTime($dt->getTimestamp() * 1000);
                }
                $match['accepted_at'] = $range;
            }

            // 1) CA total + nb total
            $pipelineTotal = [];
            if ($match) $pipelineTotal[] = ['$match' => $match];
            $pipelineTotal[] = ['$group' => [
                '_id' => null,
                'ca_total' => ['$sum' => '$prix_total'],
                'nb_total' => ['$sum' => 1],
            ]];

            $cmdTotal = new \MongoDB\Driver\Command([
                'aggregate' => $collection,
                'pipeline' => $pipelineTotal,
                'cursor' => new stdClass(),
            ]);

            $resTotal = $manager->executeCommand($mongoDb, $cmdTotal)->toArray();
            $caTotal = $resTotal ? (float)$resTotal[0]->ca_total : 0.0;
            $nbTotal = $resTotal ? (int)$resTotal[0]->nb_total : 0;

            // 2) Par menu
            $pipelineMenu = [];
            if ($match) $pipelineMenu[] = ['$match' => $match];
            $pipelineMenu[] = ['$group' => [
                '_id' => ['menu_id' => '$menu_id', 'menu_titre' => '$menu_titre'],
                'nb' => ['$sum' => 1],
                'ca' => ['$sum' => '$prix_total'],
            ]];
            $pipelineMenu[] = ['$sort' => ['nb' => -1, 'ca' => -1]];

            $cmdMenu = new \MongoDB\Driver\Command([
                'aggregate' => $collection,
                'pipeline' => $pipelineMenu,
                'cursor' => new stdClass(),
            ]);

            $resMenu = $manager->executeCommand($mongoDb, $cmdMenu)->toArray();

            foreach ($resMenu as $doc) {
                $stats[] = [
                    'menu_id' => (int)$doc->_id->menu_id,
                    'menu_titre' => (string)$doc->_id->menu_titre,
                    'nb_commandes' => (int)$doc->nb,
                    'chiffre_affaires' => (float)$doc->ca,
                ];
            }

            // 3) Volume par période (jour / semaine / mois)
            $periodFormats = [
                'day' => '%Y-%m-%d',
                'week' => '%G-W%V',
                'month' => '%Y-%m',
            ];
            if (!isset($periodFormats[$period])) {
                $period = 'day';
            }

            $pipelineVolume = [];
            if ($match) $pipelineVolume[] = ['$match' => $match];
            $pipelineVolume[] = ['$group' => [
                '_id' => [
                    '$dateToString' => [
                        'format' => $periodFormats[$period],
                        'date' => '$accepted_at',
                    ],
                ],
                'nb' => ['$sum' => 1],
            ]];
            $pipelineVolume[] = ['$sort' => ['_id' => 1]];

            $cmdVolume = new \MongoDB\Driver\Command([
                'aggregate' => $collection,
                'pipeline' => $pipelineVolume,
                'cursor' => new stdClass(),
            ]);

            $resVolume = $manager->executeCommand($mongoDb, $cmdVolume)->toArray();

            foreach ($resVolume as $doc) {
                $volumeSeries[] = [
                    'periode' => (string)$doc->_id,
                    'nb_commandes' => (int)$doc->nb,
                ];
            }

        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        require __DIR__ . '/../../views/admin/stats.php';
    }
}
