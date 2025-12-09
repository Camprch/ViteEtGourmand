<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/UserModel.php';

class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function showRegisterForm(): void
    {
        require __DIR__ . '/../../views/auth/register.php';
    }

    public function registerPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        $nom      = trim($_POST['nom'] ?? '');
        $prenom   = trim($_POST['prenom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $tel      = trim($_POST['telephone'] ?? '');
        $adresse  = trim($_POST['adresse'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];

        if ($nom === '')     $errors[] = "Le nom est obligatoire.";
        if ($prenom === '')  $errors[] = "Le prénom est obligatoire.";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }

        if ($password === '' || $confirm === '') {
            $errors[] = "Le mot de passe et sa confirmation sont obligatoires.";
        } elseif ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        } else {
            // Règles de complexité : 10+ caractères, 1 maj, 1 min, 1 chiffre, 1 spécial
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{10,}$/';
            if (!preg_match($regex, $password)) {
                $errors[] = "Le mot de passe doit faire au moins 10 caractères et contenir une majuscule, une minuscule, un chiffre et un caractère spécial.";
            }
        }

        $userModel = new UserModel($this->pdo);

        // Email déjà utilisé ?
        if ($email !== '' && $userModel->findByEmail($email)) {
            $errors[] = "Un compte existe déjà avec cet email.";
        }

        if (!empty($errors)) {
            // Affichage ultra simple pour l’instant
            echo "<h2>Erreur lors de l'inscription :</h2><ul>";
            foreach ($errors as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul>";
            echo '<a href="javascript:history.back()">Retour</a>';
            return;
        }

        // Hash du mot de passe
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $id = $userModel->create([
            'nom'        => $nom,
            'prenom'     => $prenom,
            'email'      => $email,
            'password'   => $hash,
            'telephone'  => $tel,
            'adresse'    => $adresse,
            'role'       => 'USER',
            'actif'      => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        echo "<h2>Compte créé avec succès 👍</h2>";
        echo "<p>Vous pouvez maintenant vous connecter.</p>";
        echo '<p><a href="index.php?page=login">Aller à la page de connexion</a></p>';
    }
    public function showLoginForm(): void
    {
        require __DIR__ . '/../../views/auth/login.php';
    }

    public function loginPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if ($password === '') {
            $errors[] = "Mot de passe obligatoire.";
        }

        $userModel = new UserModel($this->pdo);
        $user = null;

        if (empty($errors)) {
            $user = $userModel->findByEmail($email);

            if (!$user) {
                $errors[] = "Identifiants incorrects.";
            } elseif (!password_verify($password, $user['password'])) {
                $errors[] = "Identifiants incorrects.";
            } elseif (!(int)$user['actif']) {
                $errors[] = "Compte désactivé.";
            }
        }

        if (!empty($errors)) {
            echo "<h2>Erreur de connexion :</h2><ul>";
            foreach ($errors as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul>";
            echo '<a href="javascript:history.back()">Retour</a>';
            return;
        }

        // OK : on stocke une version simplifiée en session
        $_SESSION['user'] = [
            'id'    => (int)$user['id'],
            'nom'   => $user['nom'],
            'prenom'=> $user['prenom'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        echo "<h2>Connexion réussie 👍</h2>";
        echo "<p>Bonjour " . htmlspecialchars($user['prenom']) . " !</p>";
        echo '<p><a href="index.php?page=home">Retour à l\'accueil</a></p>';
    }

    public function logout(): void
    {
    // On ne détruit pas toute la session si on veut garder d'autres choses plus tard
    unset($_SESSION['user']);

    echo "<h2>Vous êtes maintenant déconnecté.</h2>";
    echo '<p><a href="index.php?page=home">Retour à l\'accueil</a></p>';
    }
}   