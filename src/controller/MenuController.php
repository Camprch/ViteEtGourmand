<?php
declare(strict_types=1);

// Contrôleur pour la visualisation des menus

// - index()      : Affiche la liste de tous les menus
// - show(int $id): Affiche le détail d'un menu
// - filterAjax() : Filtrage AJAX des menus (HTML partiel)

require_once __DIR__ . '/../model/MenuModel.php';

class MenuController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche la liste de tous les menus
    public function index(): void
    {
        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findAll();

        require __DIR__ . '/../../views/menu/index.php';
    }

    // Affiche le détail d'un menu
    public function show(int $id): void
    {
        if ($id <= 0) {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(404, 'Menu introuvable', 'Le menu demandé n’existe pas.');
        }

        $menuModel = new MenuModel($this->pdo);
        $menu = $menuModel->findById($id);

        if (!$menu) {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(404, 'Menu introuvable', 'Le menu demandé n’existe pas.');
        }

        $plats = $menuModel->getPlatsWithAllergenesForFront($id);

        $image = $menuModel->getMainImage($id);

        require __DIR__ . '/../../views/menu/show.php';
    }

    // Filtrage AJAX des menus (HTML partiel, sans layout)
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
            'personnes_min' => isset($_GET['personnes_min']) && $_GET['personnes_min'] !== ''
                ? (int)$_GET['personnes_min']
                : null,
        ];

        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findFiltered($filters);

        // Vue partielle (HTML uniquement, pas de header/footer)
        require __DIR__ . '/../../views/menu/_list_partial.php';
    }

}
