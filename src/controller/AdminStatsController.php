<?php
declare(strict_types=1);

// Ce contrôleur admin permet d'afficher des statistiques sur les menus commandés.
// Il démontre l'utilisation combinée d'une base SQL (source de vérité) et d'une base NoSQL (MongoDB) pour le stockage et la consultation des stats.
// Les étapes principales sont :
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

    // Vérifie que l'utilisateur courant est admin (sécurité)
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
        $collection = 'stats_menu';

        $error = null;
        $stats = [];

        try {
            // Vérifie la config MongoDB
            if (!$mongoDsn) {
                throw new RuntimeException("MONGO_DSN manquant dans .env");
            }
            if (!class_exists(\MongoDB\Driver\Manager::class)) {
                throw new RuntimeException("Extension PHP MongoDB non installée (MongoDB\\Driver\\Manager introuvable)");
            }

            $manager = new \MongoDB\Driver\Manager($mongoDsn);

            // 1) Calcul des stats depuis SQL (source de vérité)
            $sql = "
                SELECT
                    m.id AS menu_id,
                    m.titre AS menu_titre,
                    COUNT(c.id) AS nb_commandes,
                    COALESCE(SUM(c.prix_total), 0) AS chiffre_affaires
                FROM menu m
                LEFT JOIN commande c ON c.id_menu = m.id
            ";

            $params = [];
            $where = [];

            // Ajout des filtres de date si présents
            if ($dateFrom) {
                $where[] = "c.date_commande >= :from";
                $params[':from'] = $dateFrom . " 00:00:00";
            }
            if ($dateTo) {
                $where[] = "c.date_commande <= :to";
                $params[':to'] = $dateTo . " 23:59:59";
            }

            if (!empty($where)) {
                // attention: LEFT JOIN + filtres -> on filtre seulement si commande existe
                $sql .= " WHERE (" . implode(" AND ", $where) . ") OR c.id IS NULL";
            }

            $sql .= " GROUP BY m.id, m.titre ORDER BY nb_commandes DESC, m.titre ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // MongoDB sert ici de base NoSQL pour stocker et relire les statistiques affichées dans le back-office.
            // Les agrégations sont calculées côté SQL (source de vérité),
            // puis persistées en NoSQL pour consultation (cache/statistiques).
            // Cela permet de démontrer l'utilisation d'une base non relationnelle conformément aux exigences.

            // 2) Upsert dans MongoDB (NoSQL)
            $bulk = new \MongoDB\Driver\BulkWrite();

            $periodKey = ($dateFrom ?: 'null') . '_' . ($dateTo ?: 'null');

            foreach ($rows as $r) {
                $doc = [
                    'menu_id' => (int)$r['menu_id'],
                    'menu_titre' => (string)$r['menu_titre'],
                    'nb_commandes' => (int)$r['nb_commandes'],
                    'chiffre_affaires' => (float)$r['chiffre_affaires'],
                    'from' => $dateFrom ?: null,
                    'to' => $dateTo ?: null,
                    'updated_at' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
                    'period_key' => $periodKey,
                ];

                // clé unique par menu + période
                $filter = [
                    'menu_id' => $doc['menu_id'],
                    'period_key' => $periodKey,
                    'from' => $doc['from'],
                    'to' => $doc['to'],
                ];

                $bulk->update($filter, ['$set' => $doc], ['upsert' => true]);
            }

            $manager->executeBulkWrite($mongoDb . '.' . $collection, $bulk);

            // 3) Lecture depuis MongoDB (preuve NoSQL)
            $query = new \MongoDB\Driver\Query(
            ['period_key' => $periodKey],
            ['sort' => ['nb_commandes' => -1, 'menu_titre' => 1]]
            );
            $cursor = $manager->executeQuery($mongoDb . '.' . $collection, $query);

            foreach ($cursor as $doc) {
                // $doc est un objet BSON -> cast simple
                $stats[] = [
                    'menu_id' => (int)$doc->menu_id,
                    'menu_titre' => (string)$doc->menu_titre,
                    'nb_commandes' => (int)$doc->nb_commandes,
                    'chiffre_affaires' => (float)$doc->chiffre_affaires,
                ];
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }

        require __DIR__ . '/../../views/admin/stats.php';
    }
}
