<?php
declare(strict_types=1);

// Modèle pour la gestion des statistiques des commandes dans MongoDB

require_once __DIR__ . '/../config/mongo.php';

use MongoDB\BSON\UTCDateTime;

class StatsMongoModel
{
    public function logCommandeAcceptee(array $commande, array $menu): void
    // Enregistre une commande acceptée dans la collection MongoDB
    {
        $client = mongoClient();
        $db = $client->selectDatabase(mongoDbName());
        $col = $db->selectCollection('orders_stats');

        $commandeId = (int)$commande['id'];

        // anti-doublon
        $exists = $col->findOne(['commande_id' => $commandeId], ['projection' => ['_id' => 1]]);
        if ($exists) return;

        $acceptedAt = new UTCDateTime((new DateTimeImmutable('now'))->getTimestamp() * 1000);
        // insertion
        $col->insertOne([
            'commande_id' => $commandeId,
            'menu_id' => (int)$menu['id'],
            'menu_titre' => (string)$menu['titre'],
            'prix_total' => (float)$commande['prix_total'],
            'accepted_at' => $acceptedAt,
        ]);
    }
}
