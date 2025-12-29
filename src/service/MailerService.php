<?php
declare(strict_types=1);

// Configuration du service de messagerie utilisant Symfony Mailer

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class MailerService
{
    private Mailer $mailer;
    private string $fromEmail;
    private string $fromName;

    // Constructeur pour initialiser le service de messagerie
    public function __construct()
    {
        $dsn = getenv('MAILER_DSN') ?: '';
        if ($dsn === '') {
            throw new RuntimeException("MAILER_DSN manquant dans .env");
        }

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);

        $this->fromEmail = getenv('MAIL_FROM_EMAIL') ?: 'no-reply@example.com';
        $this->fromName  = getenv('MAIL_FROM_NAME') ?: 'Vite Gourmand';
    }

    // Méthode pour envoyer un e-mail
    public function send(string $toEmail, string $toName, string $subject, string $html, string $text = ''): bool
    {
        try {
            $email = (new Email())
                ->from(sprintf('%s <%s>', $this->fromName, $this->fromEmail))
                ->to(sprintf('%s <%s>', $toName, $toEmail))
                ->subject($subject)
                ->html($html);

            if ($text !== '') {
                $email->text($text);
            } else {
                $email->text(strip_tags($html));
            }

            $this->mailer->send($email);
            return true;
        } catch (Throwable $e) {
            error_log("Mailer error: " . $e->getMessage());
            return false;
        }
    }
}
