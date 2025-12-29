<?php
declare(strict_types=1);

// Contrôleur pour la gestion des employés par un administrateur.

// - Affichage de la liste des employés
// - Création d'un nouvel employé
// - Activation/désactivation d'un employé
// Chaque méthode vérifie que l'utilisateur a le rôle ADMIN.

require_once __DIR__ . '/../security/Auth.php';
require_once __DIR__ . '/../security/Csrf.php';
require_once __DIR__ . '/../model/UserModel.php';

class AdminEmployeController
{
    // Connexion PDO à la base de données
    private PDO $pdo;

    // Constructeur : injection de la connexion PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche la liste de tous les employés
    public function index(): void
    {
        Auth::requireRole(['ADMIN']);

        $userModel = new UserModel($this->pdo);
        $employes = $userModel->findAllEmployes();

        require __DIR__ . '/../../views/admin/employes.php';
    }

    // Crée un nouvel employé à partir d'un formulaire POST
    public function create(): void
    {
        Auth::requireRole(['ADMIN']); // Sécurité : accès réservé admin

        Auth::requireRole(['ADMIN']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        // Vérifie le token CSRF
        Csrf::check();

        // Récupération et nettoyage des données du formulaire
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $nom = trim($_POST['nom'] ?? 'Employe');
        $prenom = trim($_POST['prenom'] ?? 'Nouveau');

        $errors = [];

        // Validation de l'email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        // Validation du mot de passe (présence)
        if ($password === '') {
            $errors[] = "Mot de passe obligatoire.";
        }

        // Validation de la complexité du mot de passe
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{10,}$/';
        if ($password !== '' && !preg_match($regex, $password)) {
            $errors[] = "Mot de passe trop faible (10+ caractères, maj, min, chiffre, spécial).";
        }

        $userModel = new UserModel($this->pdo);
        // Vérifie que l'email n'est pas déjà utilisé
        if ($email !== '' && $userModel->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        // Affiche les erreurs éventuelles et stoppe la création
        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo '<p><a href="index.php?page=admin_employes">Retour</a></p>';
            return;
        }

        // Hash du mot de passe
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Création de l'utilisateur en base
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

    // Active ou désactive un employé (toggle)
    public function toggleActif(): void
    {
        Auth::requireRole(['ADMIN']);

        // Vérifie que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        // Vérifie le token CSRF
        Csrf::check();

        // Récupère l'id de l'employé et le nouvel état (actif/inactif)
        $id = (int)($_POST['id'] ?? 0);
        $actif = (int)($_POST['actif'] ?? 0);

        // Validation des données reçues
        if ($id <= 0 || !in_array($actif, [0, 1], true)) {
            echo "Données invalides.";
            return;
        }

        $userModel = new UserModel($this->pdo);
        $userModel->setActif($id, $actif);

        // Redirection vers la liste des employés
        header('Location: index.php?page=admin_employes&created=1');
        exit;
    }
}
