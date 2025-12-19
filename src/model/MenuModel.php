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

    /**
     * Retourne les menus filtrés dynamiquement
     */
    public function findFiltered(array $filters): array
    {
        $sql = 'SELECT id, titre, description, personnes_min, prix_par_personne
                FROM menu
                WHERE 1=1';

        $params = [];

        if (!empty($filters['theme'])) {
            $sql .= ' AND theme = :theme';
            $params[':theme'] = $filters['theme'];
        }

        if (!empty($filters['regime'])) {
            $sql .= ' AND regime = :regime';
            $params[':regime'] = $filters['regime'];
        }

        if (!empty($filters['prix_max'])) {
            $sql .= ' AND prix_par_personne <= :prix_max';
            $params[':prix_max'] = $filters['prix_max'];
        }

        if (!empty($filters['personnes_min'])) {
            $sql .= ' AND personnes_min <= :personnes_min';
            $params[':personnes_min'] = $filters['personnes_min'];
        }

        $sql .= ' ORDER BY titre ASC';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Retourne un menu par son ID
     */
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
