<?php
declare(strict_types=1);

class AdminStatsController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function requireAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || $user['role'] !== 'ADMIN') {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

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

            // 2) Upsert dans MongoDB (NoSQL)
            $bulk = new \MongoDB\Driver\BulkWrite();

            foreach ($rows as $r) {
                $doc = [
                    'menu_id' => (int)$r['menu_id'],
                    'menu_titre' => (string)$r['menu_titre'],
                    'nb_commandes' => (int)$r['nb_commandes'],
                    'chiffre_affaires' => (float)$r['chiffre_affaires'],
                    'from' => $dateFrom ?: null,
                    'to' => $dateTo ?: null,
                    'updated_at' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
                ];

                // clé unique par menu + période
                $filter = [
                    'menu_id' => $doc['menu_id'],
                    'from' => $doc['from'],
                    'to' => $doc['to'],
                ];

                $bulk->update($filter, ['$set' => $doc], ['upsert' => true]);
            }

            $manager->executeBulkWrite($mongoDb . '.' . $collection, $bulk);

            // 3) Lecture depuis MongoDB (preuve NoSQL)
            $query = new \MongoDB\Driver\Query(
                ['from' => $dateFrom ?: null, 'to' => $dateTo ?: null],
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
