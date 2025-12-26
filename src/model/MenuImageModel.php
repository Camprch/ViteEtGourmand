<?php
declare(strict_types=1);

class MenuImageModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByMenu(int $menuId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, chemin, alt_text, is_principale
             FROM menu_image
             WHERE id_menu = :id_menu
             ORDER BY is_principale DESC, id ASC"
        );
        $stmt->execute([':id_menu' => $menuId]);
        return $stmt->fetchAll();
    }

    public function create(int $menuId, string $chemin, ?string $altText, bool $isPrincipale): void
    {
        if ($isPrincipale) {
            $this->pdo->prepare(
                "UPDATE menu_image SET is_principale = 0 WHERE id_menu = :id_menu"
            )->execute([':id_menu' => $menuId]);
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO menu_image (id_menu, chemin, alt_text, is_principale)
             VALUES (:id_menu, :chemin, :alt_text, :is_principale)"
        );
        $stmt->execute([
            ':id_menu' => $menuId,
            ':chemin' => $chemin,
            ':alt_text' => $altText,
            ':is_principale' => $isPrincipale ? 1 : 0,
        ]);
    }

    public function delete(int $id): ?string
    {
        $stmt = $this->pdo->prepare(
            "SELECT chemin FROM menu_image WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $this->pdo->prepare(
            "DELETE FROM menu_image WHERE id = :id"
        )->execute([':id' => $id]);

        return $row['chemin'];
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
