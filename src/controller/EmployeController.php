<?php
declare(strict_types=1);

// Contrôleur pour la gestion des employés.

// - dashboard() : Affiche le tableau de bord employé

require_once __DIR__ . '/../security/Auth.php';

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
        Auth::requireRole(['EMPLOYE', 'ADMIN']);
    }

    // Affiche le tableau de bord employé
    public function dashboard(): void
    {
        $this->requireEmployeOrAdmin();
        require __DIR__ . '/../../views/employe/dashboard.php';
    }
}
