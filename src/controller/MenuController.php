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

    // Détail d’un menu
    public function show(int $id): void
    {
        if ($id <= 0) {
            http_response_code(404);
            echo 'Menu introuvable.';
            return;
        }

        $menuModel = new MenuModel($this->pdo);
        $menu = $menuModel->findById($id);

        if (!$menu) {
            http_response_code(404);
            echo 'Menu introuvable.';
            return;
        }

        require __DIR__ . '/../../views/menu/show.php';
    }
}
