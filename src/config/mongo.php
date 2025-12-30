<?php
declare(strict_types=1);

// Chargement de l'autoloader de Composer

require_once __DIR__ . '/../../vendor/autoload.php';

use MongoDB\Client;

function mongoClient(): Client
{
    $uri = $_ENV['MONGODB_URI'] ?? 'mongodb://root:rootpass@127.0.0.1:27017';
    return new Client($uri);
}

function mongoDbName(): string
{
    return $_ENV['MONGODB_DB'] ?? 'vite_gourmand';
}
