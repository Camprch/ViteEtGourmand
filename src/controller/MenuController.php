<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/MenuModel.php';

class MenuController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Liste de tous les menus
    public function index(): void
    {
        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findAll();

        require __DIR__ . '/../../views/menu/index.php';
    }

    // Détail d’un menu (on fera la vraie version plus tard)
    public function show(int $id): void
    {
        echo 'Page détail menu à implémenter.';
    }
}
