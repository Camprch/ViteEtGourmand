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
}
