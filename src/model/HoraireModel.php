<?php
declare(strict_types=1);

// Modèle pour la gestion des horaires d'ouverture/fermeture du restaurant.

// - findAllOrdered : récupère tous les horaires triés par jour de la semaine
// - updateJour : met à jour les horaires d'un jour donné
// - findByJour : récupère les horaires d'un jour spécifique

class HoraireModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Récupère tous les horaires, triés selon l'ordre des jours de la semaine
    public function findAllOrdered(): array
    {
        $sql = "
            SELECT jour, heure_ouverture, heure_fermeture, ferme
            FROM horaire
            ORDER BY FIELD(
                jour,
                'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'
            )
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Met à jour les horaires et l'état (fermé/ouvert) d'un jour donné
    public function updateJour(string $jour, ?string $ouverture, ?string $fermeture, int $ferme): void
    {
        $sql = "
            UPDATE horaire
            SET heure_ouverture = :ouverture,
                heure_fermeture = :fermeture,
                ferme = :ferme
            WHERE jour = :jour
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ouverture' => $ouverture,
            ':fermeture' => $fermeture,
            ':ferme' => $ferme,
            ':jour' => $jour,
        ]);
    }

    // Récupère les horaires d'un jour précis (ou null si non trouvé)
    public function findByJour(string $jour): ?array
    {
        $stmt = $this->pdo->prepare("SELECT jour, heure_ouverture, heure_fermeture, ferme FROM horaire WHERE jour = :jour");
        $stmt->execute([':jour' => $jour]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
