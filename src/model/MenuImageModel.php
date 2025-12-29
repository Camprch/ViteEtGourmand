<?php
declare(strict_types=1);

// Modèle pour la gestion des images associées à un menu.

// - findByMenu : récupère les images d'un menu
// - create : ajoute une image (et gère l'image principale)
// - delete : supprime une image et retourne son chemin
// - getMainImage : récupère l'image principale d'un menu

class MenuImageModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Récupère toutes les images d'un menu, l'image principale en premier
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

    // Ajoute une image à un menu (et gère l'unicité de l'image principale)
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

    // Supprime une image par son id et retourne son chemin (pour suppression du fichier physique)
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

    // Récupère l'image principale d'un menu (ou null si aucune)
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
