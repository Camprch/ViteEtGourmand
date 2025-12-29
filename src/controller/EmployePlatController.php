<?php

// Fonctions principales :
// - index()        : Affiche la liste des plats
// - createForm()   : Affiche le formulaire de création de plat
// - store()        : Traite la création d'un plat
// - editForm()     : Affiche le formulaire d'édition d'un plat
// - update()       : Traite la modification d'un plat
// - delete()       : Supprime un plat

declare(strict_types=1);

require_once __DIR__ . '/../model/PlatModel.php';
require_once __DIR__ . '/../security/Csrf.php';
require_once __DIR__ . '/../model/AllergeneModel.php';

class EmployePlatController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Vérifie que l'utilisateur est employé ou admin
    private function requireEmployeOrAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user || !in_array($user['role'], ['EMPLOYE', 'ADMIN'], true)) {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

    // Normalise le type de plat (ENTREE, PLAT, DESSERT)
    private function normalizeType(string $type): ?string
    {
        $type = strtoupper(trim($type));
        return in_array($type, ['ENTREE','PLAT','DESSERT'], true) ? $type : null;
    }

    // Affiche la liste des plats
    public function index(): void
    {
        $this->requireEmployeOrAdmin();
        $platModel = new PlatModel($this->pdo);
        $plats = $platModel->findAll();
        require __DIR__ . '/../../views/employe/plat_index.php';
    }

    // Affiche le formulaire de création de plat
    public function createForm(): void
    {
        $this->requireEmployeOrAdmin();

        require_once __DIR__ . '/../model/AllergeneModel.php';
        $allergeneModel = new AllergeneModel($this->pdo);
        $allergenes = $allergeneModel->findAll();

        require __DIR__ . '/../../views/employe/plat_create.php';
    }

    // Traite la création d'un plat et l'association des allergènes
    public function store(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        Csrf::check();

        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $this->normalizeType($_POST['type'] ?? '');

        $errors = [];
        if ($nom === '') $errors[] = "Nom obligatoire.";
        if ($type === null) $errors[] = "Type invalide.";

        if ($errors) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul><p><a href='javascript:history.back()'>Retour</a></p>";
            return;
        }
       
        $platModel = new PlatModel($this->pdo);
        $platModel->create($nom, $description !== '' ? $description : null, $type);

        $platId = $platModel->create($nom, $description !== '' ? $description : null, $type);

        $selectedAllergenes = $_POST['allergenes'] ?? [];
        $platModel->replaceAllergenes($platId, $selectedAllergenes);

        header("Location: index.php?page=employe_plats&created=1");
        exit;
    }

    // Affiche le formulaire d'édition d'un plat et ses allergènes
    public function editForm(): void
    {
        $this->requireEmployeOrAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "<h2>ID invalide</h2>";
            exit;
        }

        $platModel = new PlatModel($this->pdo);
        $plat = $platModel->findById($id);

        $allergeneModel = new AllergeneModel($this->pdo);
        $allergenes = $allergeneModel->findAll();

        $platAllergenes = $platModel->getAllergenesForPlat($id);
        $platAllergeneIds = [];
        foreach ($platAllergenes as $a) {
            $platAllergeneIds[(int)$a['id']] = true;
        }

        if (!$plat) {
            http_response_code(404);
            echo "<h2>Plat introuvable</h2>";
            exit;
        }

        require __DIR__ . '/../../views/employe/plat_edit.php';
    }

    // Traite la modification d'un plat et l'association des allergènes
    public function update(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        Csrf::check();

        $id = (int)($_POST['id'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $this->normalizeType($_POST['type'] ?? '');

        $errors = [];
        if ($id <= 0) $errors[] = "ID invalide.";
        if ($nom === '') $errors[] = "Nom obligatoire.";
        if ($type === null) $errors[] = "Type invalide.";

        if ($errors) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul><p><a href='javascript:history.back()'>Retour</a></p>";
            return;
        }

        $platModel = new PlatModel($this->pdo);
        $platModel->update($id, $nom, $description !== '' ? $description : null, $type);

        $selectedAllergenes = $_POST['allergenes'] ?? [];
        $platModel->replaceAllergenes($id, $selectedAllergenes);


        header("Location: index.php?page=employe_plats&updated=1");
        exit;
    }

    // Supprime un plat
    public function delete(): void
    {
        $this->requireEmployeOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        Csrf::check();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "<h2>ID invalide</h2>";
            exit;
        }

        $platModel = new PlatModel($this->pdo);
        $platModel->delete($id);

        header("Location: index.php?page=employe_plats&deleted=1");
        exit;
    }
}
