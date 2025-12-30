<?php
declare(strict_types=1);

// Logger des statistiques des commandes dans MongoDB
// Utilisé pour le suivi des commandes acceptées
// et l'analyse des ventes

class StatsMongoLogger
{
    private \MongoDB\Driver\Manager $manager;
    private string $ns; // namespace: db.collection

    public function __construct(string $dsn, string $dbName)
    {
        $this->manager = new \MongoDB\Driver\Manager($dsn);
        $this->ns = $dbName . '.orders_stats';
    }

    public function logCommandeAcceptee(int $commandeId, int $menuId, string $menuTitre, float $prixTotal): void
    {
        // Anti-doublon (commande_id unique logique)
        $query = new \MongoDB\Driver\Query(['commande_id' => $commandeId], ['limit' => 1]);
        $cursor = $this->manager->executeQuery($this->ns, $query);

        foreach ($cursor as $_) {
            return; // déjà loggée
        }

        $bulk = new \MongoDB\Driver\BulkWrite();

        $bulk->insert([
            'commande_id' => $commandeId,
            'menu_id' => $menuId,
            'menu_titre' => $menuTitre,
            'prix_total' => $prixTotal,
            'accepted_at' => new \MongoDB\BSON\UTCDateTime((new DateTimeImmutable())->getTimestamp() * 1000),
        ]);

        $this->manager->executeBulkWrite($this->ns, $bulk);
    }
}
