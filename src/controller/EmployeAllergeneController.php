<?php
declare(strict_types=1);

// Contrôleur pour la gestion des allergènes.

// - index()       : Affiche la liste des allergènes
// - createForm()  : Affiche le formulaire de création d'allergène
// - store()       : Traite la création d'un allergène
// - editForm()    : Affiche le formulaire d'édition d'un allergène
// - update()      : Traite la modification d'un allergène
// - delete()      : Supprime un allergène

require_once __DIR__ . '/../model/AllergeneModel.php';
require_once __DIR__ . '/../security/Csrf.php';

class EmployeAllergeneController
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

    // Affiche la liste des allergènes
    public function index(): void
    {
        $this->requireEmployeOrAdmin();
        $model = new AllergeneModel($this->pdo);
        $allergenes = $model->findAll();
        require __DIR__ . '/../../views/employe/allergene_index.php';
    }

    // Affiche le formulaire de création d'allergène
    public function createForm(): void
    {
        $this->requireEmployeOrAdmin();
        require __DIR__ . '/../../views/employe/allergene_create.php';
    }

    // Traite la création d'un allergène
    public function store(): void
    {
        $this->requireEmployeOrAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        Csrf::check();

        $nom = trim($_POST['nom'] ?? '');
        if ($nom === '') {
            echo "<h2>Nom obligatoire</h2><p><a href='javascript:history.back()'>Retour</a></p>";
            return;
        }

        try {
            $model = new AllergeneModel($this->pdo);
            $model->create($nom);
            header("Location: index.php?page=employe_allergenes&created=1");
            exit;
        } catch (Throwable $e) {
            echo "<h2>Erreur création</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // Affiche le formulaire d'édition d'un allergène
    public function editForm(): void
    {
        $this->requireEmployeOrAdmin();

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "<h2>ID invalide</h2>";
            exit;
        }

        $model = new AllergeneModel($this->pdo);
        $allergene = $model->findById($id);

        if (!$allergene) {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(404, 'Allergène introuvable', 'L’allergène demandé n’existe pas.');
        }

        require __DIR__ . '/../../views/employe/allergene_edit.php';
    }

    // Traite la modification d'un allergène
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
        if ($id <= 0 || $nom === '') {
            echo "<h2>Données invalides</h2><p><a href='javascript:history.back()'>Retour</a></p>";
            return;
        }

        try {
            $model = new AllergeneModel($this->pdo);
            $model->update($id, $nom);
            header("Location: index.php?page=employe_allergenes&updated=1");
            exit;
        } catch (Throwable $e) {
            echo "<h2>Erreur mise à jour</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // Supprime un allergène
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

        try {
            $model = new AllergeneModel($this->pdo);
            $ok = $model->delete($id);

            // si ON DELETE RESTRICT empêche la suppression, $ok peut rester true mais exception probable.
            header("Location: index.php?page=employe_allergenes&deleted=" . ($ok ? "1" : "0"));
            exit;
        } catch (Throwable $e) {
            // Message clair : l'allergène est utilisé
            header("Location: index.php?page=employe_allergenes&delete_error=1");
            exit;
        }
    }
}
