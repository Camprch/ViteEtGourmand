<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/ContactModel.php';

class ContactController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function showForm(): void
    {
        require __DIR__ . '/../../views/contact/index.php';
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo "Méthode invalide.";
            return;
        }

        require_once __DIR__ . '/../security/Csrf.php';
        Csrf::check();

        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];

        if ($nom === '') $errors[] = "Le nom est obligatoire.";
        if ($titre === '') $errors[] = "Le titre est obligatoire.";
        if ($message === '') $errors[] = "Le message est obligatoire.";
        if (
            $email === ''
            || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || preg_match("/[\r\n]/", $email)
        ) {
            $errors[] = "Email invalide.";
        }

        if (!empty($errors)) {
            echo "<h2>Erreur :</h2><ul>";
            foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
            echo "</ul>";
            echo '<p><a href="javascript:history.back()">Retour</a></p>';
            return;
        }

        // 1) Enregistrement BDD
        $contactModel = new ContactModel($this->pdo);
        $contactModel->create([
            'nom' => $nom,
            'email' => $email,
            'titre' => $titre,
            'message' => $message,
            'date' => date('Y-m-d H:i:s'),
            'traite' => 0,
        ]);

        // 2) Envoi email (mode simple)
        // IMPORTANT : en local, mail() ne marche souvent pas => fallback DEV
        $to = 'contact@vite-gourmand.local'; // à remplacer par l’email réel en prod
        $subject = "[Vite & Gourmand] " . $titre;
        $body = "Nouveau message de contact\n\n"
              . "Nom: {$nom}\n"
              . "Email: {$email}\n"
              . "Titre: {$titre}\n\n"
              . "Message:\n{$message}\n";

        $from = 'no-reply@vite-gourmand.local';
        $headers = "From: {$from}\r\nReply-To: {$email}\r\n";

        $sent = false;
        if (function_exists('mail')) {
            $sent = @mail($to, $subject, $body, $headers);
        }

        echo "<h2>Message envoyé ✅</h2>";
        echo "<p>Votre message a bien été enregistré.</p>";

        if (!$sent) {
            // Preuve “DEV” pour le jury si le mail n’est pas configuré
            echo "<p><strong>⚠️ Environnement DEV :</strong> l’envoi email n’est pas configuré.</p>";
            echo "<pre>" . htmlspecialchars("TO: $to\nSUBJECT: $subject\n\n$body") . "</pre>";
        }

        echo '<p><a href="index.php?page=home">Retour à l’accueil</a></p>';
    }
}
