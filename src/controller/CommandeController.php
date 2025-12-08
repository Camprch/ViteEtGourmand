<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/MenuModel.php';

class CommandeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affichage du formulaire de commande
    public function form(int $menuId): void
    {
        $menuModel = new MenuModel($this->pdo);
        $menu = $menuModel->findById($menuId);

        if (!$menu) {
            echo "Menu introuvable.";
            return;
        }

        // plus tard : vérifier authentification
        require __DIR__ . '/../../views/commande/form.php';
    }
}
