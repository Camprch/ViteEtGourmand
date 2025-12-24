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
}
