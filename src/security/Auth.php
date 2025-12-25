<?php
declare(strict_types=1);

final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::user()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'index.php?page=home';
            header('Location: index.php?page=login');
            exit;
        }
    }

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
