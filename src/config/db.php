<?php
declare(strict_types=1);

$envPath = __DIR__ . '/../../.env';

if (!file_exists($envPath)) {
    die('Fichier .env manquant. Vérifie qu’il existe à la racine du projet.');
}

$env = parse_ini_file($envPath);

$dsn    = $env['DB_DSN']  ?? null;
$dbUser = $env['DB_USER'] ?? null;
$dbPass = $env['DB_PASS'] ?? null;

if (!$dsn || !$dbUser) {
    die('Configuration DB incomplète dans .env');
}

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()));
}
