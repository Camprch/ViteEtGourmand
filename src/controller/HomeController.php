<?php

// Contrôleur pour la page d'accueil

// - index() : Affiche la page d'accueil avec les menus et les avis validés

declare(strict_types=1);

require_once __DIR__ . '/../model/MenuModel.php';
require_once __DIR__ . '/../model/AvisModel.php';

class HomeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche la page d'accueil avec les menus et les avis validés
    public function index(): void
    {
        $menuModel = new MenuModel($this->pdo);
        $menus = $menuModel->findAll();

        $avisModel = new AvisModel($this->pdo);
        $avis = $avisModel->getValidAvis();

        require __DIR__ . '/../../views/home.php';
    }
}
