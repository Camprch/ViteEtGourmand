<?php
declare(strict_types=1);

class EmployeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function requireEmployeOrAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;

        if (!$user || !in_array($user['role'], ['EMPLOYE', 'ADMIN'], true)) {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            echo '<p><a href="index.php?page=login">Se connecter</a></p>';
            exit;
        }
    }

    public function dashboard(): void
    {
        $this->requireEmployeOrAdmin();
        require __DIR__ . '/../../views/employe/dashboard.php';
    }
}
