<?php

// Fonctions principales :
// - dashboard() : Affiche le tableau de bord employé

declare(strict_types=1);

class EmployeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Vérifie que l'utilisateur est employé ou admin
    private function requireEmployeOrAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;

        if (!$user) {
            header('Location: index.php?page=login');
            exit;
        }

        if (!in_array($user['role'], ['EMPLOYE', 'ADMIN'], true)) {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

    // Affiche le tableau de bord employé
    public function dashboard(): void
    {
        $this->requireEmployeOrAdmin();
        require __DIR__ . '/../../views/employe/dashboard.php';
    }
}
