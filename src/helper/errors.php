<?php
declare(strict_types=1);

// Helper pour afficher des pages d'erreur propres.

function render_error(int $code, string $title, string $message): void
{
    http_response_code($code);

    $pageTitle = $title . ' - Vite & Gourmand';
    $errorCode = $code;
    $errorTitle = $title;
    $errorMessage = $message;

    require __DIR__ . '/../../views/error/error.php';
    exit;
}
