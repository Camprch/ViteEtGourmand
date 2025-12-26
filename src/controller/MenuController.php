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

        $plats = $menuModel->getPlatsForFront($id);

        require __DIR__ . '/../../views/menu/show.php';
    }

    // Filtrage AJAX des menus (sans layout)
    public function filterAjax(): void
    {
        // Sécurité minimale : requête GET uniquement
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            exit;
        }

        // Récupération des filtres (simples pour commencer)
        $filters = [
            'theme' => isset($_GET['theme']) ? trim((string)$_GET['theme']) : null,
            'regime' => isset($_GET['regime']) ? trim((string)$_GET['regime']) : null,
            'prix_max' => isset($_GET['prix_max']) && $_GET['prix_max'] !== ''
                ? (float)str_replace(',', '.', (string)$_GET['prix_max'])
                : null,
            'personnes_min' => isset($_GET['personnes_min']) ? (int)$_GET['personnes_min'] : null,
        ];

        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findFiltered($filters);

        // Vue partielle (HTML uniquement, pas de header/footer)
        require __DIR__ . '/../../views/menu/_list_partial.php';
    }

}
