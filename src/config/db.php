<?php
declare(strict_types=1);

// Ce fichier gère la connexion à la base de données via PDO.
// Il lit les paramètres de connexion depuis un fichier .env à la racine du projet.

// 1. Vérification de la présence du fichier .env
// 2. Lecture et nettoyage des variables d'environnement
// 3. Construction du DSN et récupération des identifiants
// 4. Connexion à la base de données avec gestion des erreurs

$envPath = __DIR__ . '/../../.env';

// 1. Vérifier que le fichier .env existe
if (!file_exists($envPath)) {
    die('Fichier .env manquant. Vérifie qu’il existe à la racine du projet.');
}

// 2. Lire le fichier .env (format INI)
$env = parse_ini_file($envPath);

if ($env === false) {
    die('Impossible de lire le fichier .env');
}

// Nettoyer les valeurs (enlever les guillemets éventuels)
foreach ($env as $k => $v) {
    if (is_string($v)) {
        $env[$k] = trim($v, "\"' ");
    }
}

// 3. Récupérer les infos de connexion
$dsn    = $env['DB_DSN']  ?? null;
$dbUser = $env['DB_USER'] ?? null;
$dbPass = $env['DB_PASS'] ?? null;

if (!$dsn || !$dbUser) {
    die('Configuration DB incomplète dans .env');
}

// 4. Connexion à la base de données avec gestion d'erreur
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
