<?php
declare(strict_types=1);

// Contrôleur pour la gestion du profil utilisateur

// - show()           : Affiche la page de profil de l'utilisateur
// - update()         : Met à jour les informations du profil
// - updatePassword() : Met à jour le mot de passe de l'utilisateur

require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../security/Csrf.php';

class ProfilController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Vérifie que l'utilisateur est connecté et retourne ses infos
    private function requireAuth(): array
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?page=login");
            exit;
        }
        return $_SESSION['user'];
    }

    // Affiche la page de profil de l'utilisateur
    public function show(): void
    {
        $sessionUser = $this->requireAuth();
        $model = new UserModel($this->pdo);
        $profileUser = $model->findById((int)$sessionUser['id']);

        if (!$profileUser) {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(404, 'Utilisateur introuvable', 'Ce compte n’existe pas.');
        }

        require __DIR__ . '/../../views/profil/index.php';
    }

    // Met à jour les informations du profil
    public function update(): void
    {
        $sessionUser = $this->requireAuth();
        Csrf::check();

        $id = (int)$sessionUser['id'];

        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telephone = trim($_POST['telephone'] ?? '');
        $adresse = trim($_POST['adresse'] ?? '');

        $errors = [];
        if ($nom === '') $errors[] = "Nom obligatoire.";
        if ($prenom === '') $errors[] = "Prénom obligatoire.";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";

        $model = new UserModel($this->pdo);

        if ($email !== '' && $model->emailExists($email, $id)) {
            $errors[] = "Email déjà utilisé.";
        }

        if ($errors) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul><p><a href='javascript:history.back()'>Retour</a></p>";
            return;
        }

        $model->updateProfile($id, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone !== '' ? $telephone : null,
            'adresse' => $adresse !== '' ? $adresse : null,
        ]);

        // MAJ session (évite incohérences UI)
        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['prenom'] = $prenom;
        $_SESSION['user']['email'] = $email;

        header("Location: index.php?page=profil&updated=1");
        exit;
    }

    // Met à jour le mot de passe de l'utilisateur (et force la reconnexion)
    public function updatePassword(): void
    {
        $sessionUser = $this->requireAuth();
        Csrf::check();

        $id = (int)$sessionUser['id'];

        $old = (string)($_POST['old_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        if ($new === '' || $new !== $confirm) {
            echo "<h2>Nouveau mot de passe invalide (confirmation différente)</h2>";
            return;
        }
        if (strlen($new) < 8) {
            echo "<h2>Mot de passe trop court (min 8)</h2>";
            return;
        }

        $model = new UserModel($this->pdo);
        $hash = $model->getPasswordHash($id);

        if (!password_verify($old, $hash)) {
            echo "<h2>Ancien mot de passe incorrect</h2>";
            return;
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $model->updatePassword($id, $newHash);

        // Sécurité : on force la reconnexion
        session_unset();
        session_destroy();

        header("Location: index.php?page=login&password_changed=1");
        exit;
    }
}
