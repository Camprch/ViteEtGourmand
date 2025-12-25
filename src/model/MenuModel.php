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
        $sql = 'SELECT id, titre, description, personnes_min, prix_par_personne, stock
                FROM menu
                WHERE stock IS NULL OR stock > 0
                ORDER BY titre ASC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Retourne les menus filtrés dynamiquement
     */
    public function findFiltered(array $filters): array
    {
        $sql = 'SELECT id, titre, description, personnes_min, prix_par_personne, stock
                FROM menu
                WHERE 1=1 AND (stock IS NULL OR stock > 0)';

        $params = [];

        if (!empty($filters['theme'])) {
            $sql .= ' AND theme = :theme';
            $params[':theme'] = $filters['theme'];
        }

        if (!empty($filters['regime'])) {
            $sql .= ' AND regime = :regime';
            $params[':regime'] = $filters['regime'];
        }

        if (isset($filters['prix_max']) && $filters['prix_max'] !== null && $filters['prix_max'] !== '') {
            $sql .= ' AND prix_par_personne <= :prix_max';
            $params[':prix_max'] = (float)$filters['prix_max'];
        }

        if (isset($filters['personnes_min']) && $filters['personnes_min'] !== null && $filters['personnes_min'] !== '') {
            $sql .= ' AND personnes_min <= :personnes_min';
            $params[':personnes_min'] = (int)$filters['personnes_min'];
        }

        $sql .= ' ORDER BY titre ASC';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if (in_array($key, [':prix_max'], true)) {
                $stmt->bindValue($key, (float)$value, PDO::PARAM_STR); // PDO n'a pas float
            } elseif (in_array($key, [':personnes_min'], true)) {
                $stmt->bindValue($key, (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, (string)$value, PDO::PARAM_STR);
            }
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

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO menu (
                titre, description, theme, prix_par_personne, personnes_min,
                conditions_particulieres, regime, stock, created_at
            ) VALUES (
                :titre, :description, :theme, :prix_par_personne, :personnes_min,
                :conditions_particulieres, :regime, :stock, NOW()
            )
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':theme' => $data['theme'],
            ':prix_par_personne' => $data['prix_par_personne'],
            ':personnes_min' => $data['personnes_min'],
            ':conditions_particulieres' => $data['conditions_particulieres'],
            ':regime' => $data['regime'],
            ':stock' => $data['stock'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}
