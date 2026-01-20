<?php
declare(strict_types=1);

// Classe utilitaire pour la gestion de la protection CSRF (Cross-Site Request Forgery).

// - token : génère ou retourne le token CSRF de la session
// - check : vérifie la validité du token CSRF lors d'une requête POST

final class Csrf
{
    // Retourne le token CSRF de la session (le crée si besoin)
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    // Vérifie le token CSRF lors d'une requête POST (403 si invalide)
    public static function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $sent = $_POST['_csrf'] ?? '';
        $valid = $_SESSION['_csrf'] ?? '';

        if (!$sent || !$valid || !hash_equals($valid, (string)$sent)) {
            require_once __DIR__ . '/../helper/errors.php';
            render_error(403, 'Action refusée', 'Votre session a expiré ou la requête est invalide.');
        }

        unset($_SESSION['_csrf']);
    }
}
