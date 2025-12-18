<?php
declare(strict_types=1);

class AvisModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
{
        $this->pdo = $pdo;
}

    /**
     * Retourne les avis validés (les plus récents d'abord)
     */
    public function getValidAvis(): array
{
        $stmt = $this->pdo->query("
            SELECT a.note, a.commentaire, a.date, u.prenom
            FROM avis AS a
            INNER JOIN user AS u ON u.id = a.id_user
            WHERE a.valide = 1
            ORDER BY a.date DESC
            LIMIT 5
        ");

        return $stmt->fetchAll();
    }

    /**
     * Vérifie si un avis existe déjà pour une commande
     */
    public function existsForCommande(int $commandeId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM avis WHERE id_commande = :id_commande LIMIT 1"
        );
        $stmt->execute([
            ':id_commande' => $commandeId
        ]);

        return (bool)$stmt->fetch();
    }

    /**
     * Crée un avis (non validé par défaut)
     */
    public function create(array $data): int
{
    $sql = "
        INSERT INTO avis (id_user, id_commande, id_menu, note, commentaire, date, valide)
        VALUES (:id_user, :id_commande, :id_menu, :note, :commentaire, NOW(), 0)
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);

    return (int)$this->pdo->lastInsertId();
}

    public function getPendingAvis(): array
{
    $sql = "
        SELECT a.id, a.note, a.commentaire, a.date,
               u.prenom, u.nom,
               m.titre AS menu_titre
        FROM avis a
        LEFT JOIN user u ON u.id = a.id_user
        LEFT JOIN menu m ON m.id = a.id_menu
        WHERE CAST(a.valide AS UNSIGNED) = 0
        ORDER BY a.date DESC
    ";

    return $this->pdo->query($sql)->fetchAll();
}

    public function setValid(int $avisId): void
    {
        $stmt = $this->pdo->prepare("UPDATE avis SET valide = 1 WHERE id = :id");
        $stmt->execute([':id' => $avisId]);
    }
}