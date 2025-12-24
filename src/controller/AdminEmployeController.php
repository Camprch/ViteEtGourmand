<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/UserModel.php';

class AdminEmployeController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function requireAdmin(): void
    {
        $user = $_SESSION['user'] ?? null;

        if (!$user || $user['role'] !== 'ADMIN') {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();

        $userModel = new UserModel($this->pdo);
        $employes = $userModel->findAllEmployes();

        require __DIR__ . '/../../views/admin/employes.php';
    }

    public function create(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $nom = trim($_POST['nom'] ?? 'Employe');
        $prenom = trim($_POST['prenom'] ?? 'Nouveau');

        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if ($password === '') {
            $errors[] = "Mot de passe obligatoire.";
        }

        $userModel = new UserModel($this->pdo);
        if ($email !== '' && $userModel->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo '<p><a href="index.php?page=admin_employes">Retour</a></p>';
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $userModel->create([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'password' => $hash,
            'telephone' => null,
            'adresse' => null,
            'role' => 'EMPLOYE',
            'actif' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Pour l’ECF : preuve de notification (DEV)
        echo "<h2>Employé créé ✅</h2>";
        echo "<p>Un compte employé a été créé. (En prod, un email de notification serait envoyé.)</p>";
        echo "<p><a href='index.php?page=admin_employes'>Retour</a></p>";
    }

    public function toggleActif(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $actif = (int)($_POST['actif'] ?? 0);

        if ($id <= 0 || !in_array($actif, [0, 1], true)) {
            echo "Données invalides.";
            return;
        }

        $userModel = new UserModel($this->pdo);
        $userModel->setActif($id, $actif);

        header('Location: index.php?page=admin_employes');
        exit;
    }
}
