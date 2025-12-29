<?php
declare(strict_types=1);

// Ce contrôleur gère les pages d'administration accessibles uniquement aux utilisateurs ayant le rôle ADMIN.
// Il vérifie l'accès et affiche la vue du dashboard admin.

require_once __DIR__ . '/../security/Auth.php';

class AdminController
{
    // Propriété pour stocker la connexion PDO à la base de données
    private PDO $pdo;

    // Constructeur : reçoit la connexion PDO en paramètre
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche le tableau de bord admin (dashboard)
    public function dashboard(): void
    {
        // Vérifie que l'utilisateur a le rôle ADMIN (sécurité)
        Auth::requireRole(['ADMIN']);
        // Affiche la vue du dashboard admin
        require __DIR__ . '/../../views/admin/dashboard.php';
    }
}
