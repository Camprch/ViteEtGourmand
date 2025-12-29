<?php
declare(strict_types=1);

// Contrôleur pour tester l'envoi d'emails via SMTP

require_once __DIR__ . '/../service/MailerService.php';

class TestMailController
{
    private PDO $pdo;

    // Injection de dépendance de la connexion PDO
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Méthode pour envoyer un email de test
    public function send(): void
    {
        $to = $_GET['to'] ?? '';
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo "<h2>Paramètre manquant</h2>";
            echo "<p>Utilise: index.php?page=test_mail&to=ton@email.com</p>";
            return;
        }

        $mailer = new MailerService();
        $ok = $mailer->send(
            $to,
            'Test SMTP',
            'Test SMTP Vite Gourmand',
            '<p>Si tu lis ce mail, le SMTP fonctionne 🎉</p>'
        );

        echo $ok ? "<h2>✅ Email envoyé</h2>" : "<h2>❌ Échec envoi</h2>";
    }
}
