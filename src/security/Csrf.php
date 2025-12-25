<?php
declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function check(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $sent = $_POST['_csrf'] ?? '';
        $valid = $_SESSION['_csrf'] ?? '';

        if (!$sent || !$valid || !hash_equals($valid, (string)$sent)) {
            http_response_code(403);
            echo "<h2>Action refusée (CSRF)</h2>";
            exit;
        }

        unset($_SESSION['_csrf']);
    }
}