<?php
declare(strict_types=1);

// Contrôleur de gestion du formulaire de contact.

// - showForm()  : Affiche le formulaire de contact
// - submit()    : Traite l'envoi du formulaire, enregistre le message et tente un envoi email

require_once __DIR__ . '/../model/ContactModel.php';
require_once __DIR__ . '/../service/MailerService.php';

class ContactController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Affiche le formulaire de contact
    public function showForm(): void
    {
        require __DIR__ . '/../../views/contact/index.php';
    }

    // Traite la soumission du formulaire de contact, enregistre le message et tente un envoi email
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

        // 2) Envoi email via SMTP (MailerService)
        $to = getenv('MAIL_NOTIFY_EMAIL') ?: getenv('MAIL_FROM_EMAIL') ?: 'contact@vite-gourmand.local';
        $toName = getenv('MAIL_NOTIFY_NAME') ?: 'Equipe Vite Gourmand';
        $subject = "[Vite & Gourmand] " . $titre;
        $text = "Nouveau message de contact\n\n"
              . "Nom: {$nom}\n"
              . "Email: {$email}\n"
              . "Titre: {$titre}\n\n"
              . "Message:\n{$message}\n";
        $html = "<p><strong>Nouveau message de contact</strong></p>"
              . "<p><strong>Nom :</strong> " . htmlspecialchars($nom) . "<br>"
              . "<strong>Email :</strong> " . htmlspecialchars($email) . "<br>"
              . "<strong>Titre :</strong> " . htmlspecialchars($titre) . "</p>"
              . "<p><strong>Message :</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

        $sent = false;
        try {
            $mailer = new MailerService();
            $sent = $mailer->send($to, $toName, $subject, $html, $text);
        } catch (Throwable $e) {
            $sent = false;
        }

        echo "<h2>Message envoyé ✅</h2>";
        echo "<p>Votre message a bien été enregistré.</p>";

        if (!$sent) {
            // "Preuve DEV” si le mail n’est pas configuré
            echo "<p><strong>⚠️ Environnement DEV :</strong> l’envoi email n’est pas configuré.</p>";
            echo "<pre>" . htmlspecialchars("TO: $to\nSUBJECT: $subject\n\n$text") . "</pre>";
        }

        echo '<p><a href="index.php?page=home">Retour à l’accueil</a></p>';
    }
}
