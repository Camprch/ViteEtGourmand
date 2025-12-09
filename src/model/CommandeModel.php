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

    public function findByUserId(int $userId): array
    {
    $sql = "
        SELECT c.id,
               c.date_commande,
               c.date_prestation,
               c.prix_total,
               c.statut_courant,
               m.titre AS menu_titre
        FROM commande c
        INNER JOIN menu m ON c.id_menu = m.id
        WHERE c.id_user = :id_user
        ORDER BY c.date_commande DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id_user', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
    }

    public function findByIdForUser(int $commandeId, int $userId): ?array
    {
    $sql = "
        SELECT c.*,
               m.titre AS menu_titre
        FROM commande c
        INNER JOIN menu m ON c.id_menu = m.id
        WHERE c.id = :id
          AND c.id_user = :id_user
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $commandeId, PDO::PARAM_INT);
    $stmt->bindValue(':id_user', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch();
    return $row ?: null;
    }

}
