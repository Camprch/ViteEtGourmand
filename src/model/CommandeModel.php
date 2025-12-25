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

    public function updateStatus(int $id, string $newStatus): bool
    {
    $sql = "UPDATE commande SET statut_courant = :statut WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':statut' => $newStatus,
        ':id'     => $id,
    ]);
    }

    public function getStatutHistorique(int $commandeId): array
    {
    $sql = "
        SELECT statut, date_heure, commentaire, id_employe
        FROM commande_statut
        WHERE id_commande = :id_commande
        ORDER BY date_heure ASC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id_commande', $commandeId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
    }

    public function addStatutHistorique(
        int $commandeId,
        string $statut,
        ?int $idEmploye = null,
        ?string $commentaire = null
    ): void {
        
        if ($idEmploye !== null && $idEmploye <= 0) {
            $idEmploye = null;
        }

        $sql = "
            INSERT INTO commande_statut (id_commande, id_employe, statut, commentaire, date_heure)
            VALUES (:id_commande, :id_employe, :statut, :commentaire, NOW())
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_commande' => $commandeId,
            ':id_employe'  => $idEmploye,
            ':statut'      => $statut,
            ':commentaire' => $commentaire,
        ]);
    }

    public function findAllForEmploye(?string $statut = null): array
    {
        $sql = "
            SELECT c.id, c.date_commande, c.date_prestation, c.heure_prestation,
                c.ville, c.nb_personnes, c.prix_total, c.statut_courant,
                u.nom, u.prenom, u.email,
                m.titre AS menu_titre
            FROM commande c
            INNER JOIN user u ON c.id_user = u.id
            INNER JOIN menu m ON c.id_menu = m.id
            WHERE 1=1
        ";

        $params = [];

        if ($statut) {
            $sql .= " AND c.statut_courant = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= " ORDER BY c.date_commande DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByIdForEmploye(int $commandeId): ?array
    {
        $sql = "
            SELECT c.*, 
                u.nom, u.prenom, u.email, u.telephone,
                m.titre AS menu_titre
            FROM commande c
            INNER JOIN user u ON c.id_user = u.id
            INNER JOIN menu m ON c.id_menu = m.id
            WHERE c.id = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $commandeId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
