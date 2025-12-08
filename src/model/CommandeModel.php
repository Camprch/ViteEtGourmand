<?php
declare(strict_types=1);

class CommandeModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Crée une commande et retourne l'ID inséré
     */
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO commande (
                id_user,
                id_menu,
                date_commande,
                date_prestation,
                heure_prestation,
                adresse_prestation,
                ville,
                code_postal,
                nb_personnes,
                prix_menu_total,
                reduction_appliquee,
                frais_livraison,
                prix_total
                -- statut_courant est laissé à la valeur par défaut EN_ATTENTE
            ) VALUES (
                :id_user,
                :id_menu,
                NOW(),
                :date_prestation,
                :heure_prestation,
                :adresse_prestation,
                :ville,
                :code_postal,
                :nb_personnes,
                :prix_menu_total,
                :reduction_appliquee,
                :frais_livraison,
                :prix_total
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int)$this->pdo->lastInsertId();
    }
}
