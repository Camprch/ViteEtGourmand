<?php
declare(strict_types=1);

class AdminController
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
            echo '<p><a href="index.php?page=login">Se connecter</a></p>';
            exit;
        }
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        require __DIR__ . '/../../views/admin/dashboard.php';
    }
}
