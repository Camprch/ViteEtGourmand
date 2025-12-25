<?php
declare(strict_types=1);

$envPath = __DIR__ . '/../../.env';

if (!file_exists($envPath)) {
    die('Fichier .env manquant. Vérifie qu’il existe à la racine du projet.');
}

$env = parse_ini_file($envPath);

if ($env === false) {
    die('Impossible de lire le fichier .env');
}

foreach ($env as $k => $v) {
    if (is_string($v)) {
        $env[$k] = trim($v, "\"' ");
    }
}

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
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()));
}
