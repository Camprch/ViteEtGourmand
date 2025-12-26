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

    public function findAllForBackoffice(): array
    {
        $sql = 'SELECT id, titre, theme, regime, personnes_min, prix_par_personne, stock, created_at
                FROM menu
                ORDER BY created_at DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE menu SET
                    titre = :titre,
                    description = :description,
                    theme = :theme,
                    prix_par_personne = :prix_par_personne,
                    personnes_min = :personnes_min,
                    conditions_particulieres = :conditions_particulieres,
                    regime = :regime,
                    stock = :stock
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':theme' => $data['theme'],
            ':prix_par_personne' => $data['prix_par_personne'],
            ':personnes_min' => $data['personnes_min'],
            ':conditions_particulieres' => $data['conditions_particulieres'],
            ':regime' => $data['regime'],
            ':stock' => $data['stock'],
        ]);
    }

    public function setStock(int $id, ?int $stock): bool
    {
        $stmt = $this->pdo->prepare("UPDATE menu SET stock = :stock WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        if ($stock === null) {
            $stmt->bindValue(':stock', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
        }
        return $stmt->execute();
    }

    public function getPlatsForMenu(int $menuId): array
    {
        $sql = "SELECT mp.id_plat, mp.ordre, p.nom, p.type
                FROM menu_plat mp
                JOIN plat p ON p.id = mp.id_plat
                WHERE mp.id_menu = :id_menu
                ORDER BY
                    (mp.ordre IS NULL) ASC,
                    mp.ordre ASC,
                    p.type ASC,
                    p.nom ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_menu' => $menuId]);
        return $stmt->fetchAll();
    }

    /**
     * $items = [
     *   ['id_plat' => 12, 'ordre' => 1],
     *   ['id_plat' => 7,  'ordre' => 2],
     * ]
     */
    public function replacePlats(int $menuId, array $items): void
    {
        $this->pdo->beginTransaction();

        try {
            // On remplace tout (simple, fiable, jury-proof)
            $del = $this->pdo->prepare("DELETE FROM menu_plat WHERE id_menu = :id_menu");
            $del->execute([':id_menu' => $menuId]);

            $ins = $this->pdo->prepare("
                INSERT INTO menu_plat (id_menu, id_plat, ordre)
                VALUES (:id_menu, :id_plat, :ordre)
            ");

            foreach ($items as $it) {
                $ordre = $it['ordre'];
                $ins->bindValue(':id_menu', $menuId, PDO::PARAM_INT);
                $ins->bindValue(':id_plat', (int)$it['id_plat'], PDO::PARAM_INT);

                if ($ordre === null) {
                    $ins->bindValue(':ordre', null, PDO::PARAM_NULL);
                } else {
                    $ins->bindValue(':ordre', (int)$ordre, PDO::PARAM_INT);
                }

                $ins->execute();
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
    
    public function getPlatsWithAllergenesForFront(int $menuId): array
    {
        $sql = "SELECT
                    p.id AS plat_id,
                    p.nom AS plat_nom,
                    p.description AS plat_description,
                    p.type AS plat_type,
                    mp.ordre AS plat_ordre,
                    a.id AS allergene_id,
                    a.nom AS allergene_nom
                FROM menu_plat mp
                JOIN plat p ON p.id = mp.id_plat
                LEFT JOIN plat_allergene pa ON pa.id_plat = p.id
                LEFT JOIN allergene a ON a.id = pa.id_allergene
                WHERE mp.id_menu = :id_menu
                ORDER BY
                    (mp.ordre IS NULL) ASC,
                    mp.ordre ASC,
                    FIELD(p.type,'ENTREE','PLAT','DESSERT'),
                    p.nom ASC,
                    a.nom ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_menu' => $menuId]);
        $rows = $stmt->fetchAll();

        // regroupe : plat -> [allergenes...]
        $plats = [];
        foreach ($rows as $r) {
            $pid = (int)$r['plat_id'];
            if (!isset($plats[$pid])) {
                $plats[$pid] = [
                    'id' => $pid,
                    'nom' => $r['plat_nom'],
                    'description' => $r['plat_description'],
                    'type' => $r['plat_type'],
                    'ordre' => $r['plat_ordre'] !== null ? (int)$r['plat_ordre'] : null,
                    'allergenes' => [],
                ];
            }

            if ($r['allergene_id'] !== null) {
                $plats[$pid]['allergenes'][] = [
                    'id' => (int)$r['allergene_id'],
                    'nom' => $r['allergene_nom'],
                ];
            }
        }

        return array_values($plats);
    }

    public function getMainImage(int $menuId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT chemin, alt_text
            FROM menu_image
            WHERE id_menu = :id_menu AND is_principale = 1
            LIMIT 1"
        );
        $stmt->execute([':id_menu' => $menuId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
