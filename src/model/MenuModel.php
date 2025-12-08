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

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, titre, description, theme, personnes_min, prix_par_personne,
                       conditions_particulieres, regime, stock
                FROM menu
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $menu = $stmt->fetch();

        return $menu ?: null;
    }
}
