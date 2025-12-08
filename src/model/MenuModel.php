<?php
declare(strict_types=1);

class MenuModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retourne tous les menus, pour l’instant sans filtres.
     */
    public function findAll(): array
    {
        $sql = 'SELECT id, titre, description, personnes_min, prix_par_personne 
                FROM menu
                ORDER BY titre ASC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}
