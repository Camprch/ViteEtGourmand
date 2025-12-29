<?php

// Classe utilitaire pour la gestion de l'authentification et des droits d'accès.

// - user : retourne l'utilisateur connecté (ou null)
// - requireLogin : force la connexion pour accéder à une page
// - requireRole : force un rôle spécifique pour accéder à une page

declare(strict_types=1);

final class Auth
{
    // Retourne l'utilisateur connecté (ou null si non connecté)
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    // Force la connexion pour accéder à la page (redirige sinon)
    public static function requireLogin(): void
    {
        if (!self::user()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php?page=home';
            header('Location: index.php?page=login');
            exit;
        }
    }

    // Force un rôle spécifique pour accéder à la page (403 sinon)
    public static function requireRole(array $roles): void
    {
        $u = self::user();
        if (!$u || !in_array($u['role'], $roles, true)) {
            http_response_code(403);
            echo "<h2>Accès refusé</h2>";
            exit;
        }
    }
}
