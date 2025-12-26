<?php
declare(strict_types=1);

class HoraireModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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

    public function findByJour(string $jour): ?array
    {
        $stmt = $this->pdo->prepare("SELECT jour, heure_ouverture, heure_fermeture, ferme FROM horaire WHERE jour = :jour");
        $stmt->execute([':jour' => $jour]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
