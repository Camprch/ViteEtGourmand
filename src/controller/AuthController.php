<?php
declare(strict_types=1);

// Ce contrôleur gère toute l'authentification utilisateur :
// - Inscription
// - Connexion
// - Déconnexion
// - Mot de passe oublié et réinitialisation
// Chaque méthode correspond à une action liée à l'authentification.

require_once __DIR__ . '/../model/UserModel.php';
require_once __DIR__ . '/../security/Csrf.php';

class AuthController
{
    // Connexion PDO à la base de données
    private PDO $pdo;

    // Constructeur : injection de la connexion PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche le formulaire d'inscription
    public function showRegisterForm(): void
    {
        require __DIR__ . '/../../views/auth/register.php';
    }

    // Traite la soumission du formulaire d'inscription
    public function registerPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();

        // Récupération et nettoyage des données du formulaire
        $nom      = trim($_POST['nom'] ?? '');
        $prenom   = trim($_POST['prenom'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $tel      = trim($_POST['telephone'] ?? '');
        $adresse  = trim($_POST['adresse'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $errors = [];

        // Validation des champs obligatoires
        if ($nom === '')     $errors[] = "Le nom est obligatoire.";
        if ($prenom === '')  $errors[] = "Le prénom est obligatoire.";
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }

        // Validation du mot de passe et confirmation
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

        // Vérifie si l'email est déjà utilisé
        if ($email !== '' && $userModel->findByEmail($email)) {
            $errors[] = "Un compte existe déjà avec cet email.";
        }

        // Affichage des erreurs éventuelles
        if (!empty($errors)) {
            echo "<h2>Erreur lors de l'inscription :</h2><ul>";
            foreach ($errors as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul>";
            echo '<a href="javascript:history.back()">Retour</a>';
            return;
        }

        // Hash du mot de passe puis création de l'utilisateur
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

    // Affiche le formulaire de connexion
    public function showLoginForm(): void
    {
        require __DIR__ . '/../../views/auth/login.php';
    }

    // Traite la soumission du formulaire de connexion
    public function loginPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        // Validation des champs
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if ($password === '') {
            $errors[] = "Mot de passe obligatoire.";
        }

        $userModel = new UserModel($this->pdo);
        $user = null;

        // Vérification des identifiants
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

        // Affichage des erreurs éventuelles
        if (!empty($errors)) {
            echo "<h2>Erreur de connexion :</h2><ul>";
            foreach ($errors as $e) {
                echo "<li>" . htmlspecialchars($e) . "</li>";
            }
            echo "</ul>";
            echo '<a href="javascript:history.back()">Retour</a>';
            return;
        }

        // Connexion réussie : on stocke l'utilisateur en session
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id'    => (int)$user['id'],
            'nom'   => $user['nom'],
            'prenom'=> $user['prenom'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        // Redirection après connexion
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?page=home';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit;
    }

    // Affiche le formulaire "mot de passe oublié"
    public function showForgotPasswordForm(): void
    {
        require __DIR__ . '/../../views/auth/forgot_password.php';
    }

    // Traite la demande de réinitialisation de mot de passe
    public function forgotPasswordPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();

        $email = trim($_POST['email'] ?? '');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<p>Email invalide.</p>";
            echo '<p><a href="javascript:history.back()">Retour</a></p>';
            return;
        }

        $userModel = new UserModel($this->pdo);
        $user = $userModel->findByEmail($email);

        // Réponse neutre (anti-enumération) : on ne dit jamais si l'email existe
        if ($user) {
            $token = bin2hex(random_bytes(32));

            $userModel->createPasswordResetToken((int)$user['id'], $token);

            // Lien de reset (en vrai il faut l’URL publique en prod)
            $link = 'index.php?page=reset_password&token=' . urlencode($token);

            // En dev : on affiche le lien (et plus tard on remplacera par un vrai mail)
            echo "<h2>Demande prise en compte</h2>";
            echo "<p>Si un compte existe pour cet email, un lien de réinitialisation a été envoyé.</p>";
            echo "<p><strong>Lien (DEV) :</strong> <a href=\"" . htmlspecialchars($link) . "\">Réinitialiser le mot de passe</a></p>";
            return;
        }

        // Toujours la même réponse pour éviter de révéler si l'email existe
        echo "<h2>Demande prise en compte</h2>";
        echo "<p>Si un compte existe pour cet email, un lien de réinitialisation a été envoyé.</p>";
    }

    // Affiche le formulaire de réinitialisation de mot de passe
    public function showResetPasswordForm(): void
    {
        $token = trim($_GET['token'] ?? '');
        if ($token === '') {
            http_response_code(400);
            echo "Token manquant.";
            return;
        }

        require __DIR__ . '/../../views/auth/reset_password.php';
    }

    // Traite la soumission du nouveau mot de passe
    public function resetPasswordPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        Csrf::check();

        $token = trim($_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if ($token === '') {
            echo "<p>Token manquant.</p>";
            return;
        }

        $errors = [];
        // Validation du mot de passe et confirmation
        if ($password === '' || $confirm === '') {
            $errors[] = "Le mot de passe et sa confirmation sont obligatoires.";
        } elseif ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        } else {
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{10,}$/';
            if (!preg_match($regex, $password)) {
                $errors[] = "Le mot de passe doit faire au moins 10 caractères et contenir une majuscule, une minuscule, un chiffre et un caractère spécial.";
            }
        }

        // Affichage des erreurs éventuelles
        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo '<p><a href="javascript:history.back()">Retour</a></p>';
            return;
        }

        $userModel = new UserModel($this->pdo);

        // Vérifie la validité du token
        $resetRow = $userModel->findValidPasswordResetToken($token);
        if (!$resetRow) {
            echo "<p>Lien invalide ou expiré.</p>";
            return;
        }

        // Met à jour le mot de passe et marque le token comme utilisé
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userModel->updatePassword((int)$resetRow['id_user'], $hash);
        $userModel->markPasswordResetTokenUsed((int)$resetRow['id'], date('Y-m-d H:i:s'));

        echo "<h2>Mot de passe mis à jour ✅</h2>";
        echo '<p><a href="index.php?page=login">Se connecter</a></p>';
    }

    // Déconnecte l'utilisateur
    public function logout(): void
    {
    
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();

        echo "<h2>Vous êtes maintenant déconnecté.</h2>";
        echo '<p><a href="index.php?page=home">Retour à l\'accueil</a></p>';
    }
}