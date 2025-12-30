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
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

    // Récupère une variable d'environnement depuis le .env
    private function env(string $key, ?string $default = null): ?string
    {
        $envPath = __DIR__ . '/../../.env';
        if (!file_exists($envPath)) return $default;

        $env = parse_ini_file($envPath);
        $val = $env[$key] ?? $default;

        if (is_string($val)) {
            $val = trim($val, "\"'");
        }
        return $val;
    }

    // Affiche la page de statistiques (calcul, stockage, lecture)
    public function index(): void
    {
        $this->requireAdmin();

        $dateFrom = $_GET['from'] ?? null; // YYYY-MM-DD
        $dateTo   = $_GET['to'] ?? null;   // YYYY-MM-DD

        $mongoDsn = $this->env('MONGO_DSN');
        $mongoDb  = $this->env('MONGO_DB', 'vite_gourmand');
        $collection = 'orders_stats';

        $error = null;
        $stats = [];
        $caTotal = 0.0;
        $nbTotal = 0;

        try {
            if (!$mongoDsn) {
                throw new RuntimeException("MONGO_DSN manquant dans .env");
            }
            if (!class_exists(\MongoDB\Driver\Manager::class)) {
                throw new RuntimeException("Extension PHP MongoDB non installée");
            }

            $manager = new \MongoDB\Driver\Manager($mongoDsn);

            $match = [];
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

        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        require __DIR__ . '/../../views/admin/stats.php';
    }
}

