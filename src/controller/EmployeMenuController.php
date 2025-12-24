<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/MenuModel.php';

class EmployeMenuController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function requireEmployeOrAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;

        if (!$user || !in_array($user['role'], ['EMPLOYE', 'ADMIN'], true)) {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

    public function createForm(): void
    {
        $this->requireEmployeOrAdmin();
        require __DIR__ . '/../../views/employe/menu_create.php';
    }

    public function store(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $theme = trim($_POST['theme'] ?? '');
        $regime = trim($_POST['regime'] ?? '');
        $prix = (float)($_POST['prix_par_personne'] ?? 0);
        $personnesMin = (int)($_POST['personnes_min'] ?? 0);
        $conditions = trim($_POST['conditions_particulieres'] ?? '');
        $stock = isset($_POST['stock']) && $_POST['stock'] !== '' ? (int)$_POST['stock'] : null;

        $errors = [];
        if ($titre === '') $errors[] = "Titre obligatoire.";
        if ($description === '') $errors[] = "Description obligatoire.";
        if ($prix <= 0) $errors[] = "Prix par personne invalide.";
        if ($personnesMin <= 0) $errors[] = "Nombre minimum de personnes invalide.";

        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo '<p><a href="javascript:history.back()">Retour</a></p>';
            return;
        }

        $menuModel = new MenuModel($this->pdo);
        $id = $menuModel->create([
            'titre' => $titre,
            'description' => $description,
            'theme' => $theme !== '' ? $theme : null,
            'prix_par_personne' => $prix,
            'personnes_min' => $personnesMin,
            'conditions_particulieres' => $conditions !== '' ? $conditions : null,
            'regime' => $regime !== '' ? $regime : null,
            'stock' => $stock,
        ]);

        echo "<h2>Menu créé ✅</h2>";
        echo "<p>ID : " . (int)$id . "</p>";
        echo "<p><a href='index.php?page=employe_menu_create'>Créer un autre menu</a></p>";
        echo "<p><a href='index.php?page=dashboard_employe'>Retour dashboard</a></p>";
    }
}
