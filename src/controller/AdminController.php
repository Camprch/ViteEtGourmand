<?php
declare(strict_types=1);

require_once __DIR__ . '/../security/Auth.php';

class AdminController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function dashboard(): void
    {
        Auth::requireRole(['ADMIN']);
        require __DIR__ . '/../../views/admin/dashboard.php';
    }
}
