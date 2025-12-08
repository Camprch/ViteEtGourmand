<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/MenuModel.php';

class HomeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function index(): void
    {
        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findAll();

        // On passe les données à la vue
        require __DIR__ . '/../../views/home.php';
    }
}
