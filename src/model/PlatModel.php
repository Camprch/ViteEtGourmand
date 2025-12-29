<?php

// Modèle pour la gestion des plats (entrées, plats, desserts).

// - findAll : liste tous les plats
// - findById : récupère un plat par son id
// - create : ajoute un plat
// - update : modifie un plat
// - delete : supprime un plat
// - getAllergenesForPlat : liste les allergènes d'un plat
// - replaceAllergenes : remplace les allergènes d'un plat

declare(strict_types=1);

class PlatModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Récupère tous les plats, triés par type et nom
    public function findAll(): array
    {
        $sql = "SELECT id, nom, description, type
                FROM plat
                ORDER BY type ASC, nom ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Récupère un plat par son identifiant (ou null si non trouvé)
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nom, description, type FROM plat WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Ajoute un nouveau plat et retourne son id
    public function create(string $nom, ?string $description, string $type): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO plat (nom, description, type) VALUES (:nom, :description, :type)");
        $stmt->execute([
            ':nom' => $nom,
            ':description' => $description,
            ':type' => $type,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    // Met à jour les informations d'un plat
    public function update(int $id, string $nom, ?string $description, string $type): bool
    {
        $stmt = $this->pdo->prepare("UPDATE plat SET nom = :nom, description = :description, type = :type WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':description' => $description,
            ':type' => $type,
        ]);
    }

    // Supprime un plat (les liens sont supprimés automatiquement en base)
    public function delete(int $id): bool
    {
        // OK car menu_plat et plat_allergene ont ON DELETE CASCADE côté plat
        $stmt = $this->pdo->prepare("DELETE FROM plat WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // Récupère la liste des allergènes associés à un plat
    public function getAllergenesForPlat(int $platId): array
    {
        $sql = "SELECT a.id, a.nom
                FROM plat_allergene pa
                JOIN allergene a ON a.id = pa.id_allergene
                WHERE pa.id_plat = :id_plat
                ORDER BY a.nom ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_plat' => $platId]);
        return $stmt->fetchAll();
    }

    // Remplace la liste des allergènes d'un plat (suppression puis insertion, sans doublons)
    public function replaceAllergenes(int $platId, array $allergeneIds): void
    {
        $this->pdo->beginTransaction();
        try {
            $del = $this->pdo->prepare("DELETE FROM plat_allergene WHERE id_plat = :id_plat");
            $del->execute([':id_plat' => $platId]);

            $ins = $this->pdo->prepare("INSERT INTO plat_allergene (id_plat, id_allergene) VALUES (:id_plat, :id_allergene)");

            // dédoublonne + cast int
            $uniq = [];
            foreach ($allergeneIds as $aidRaw) {
                $aid = (int)$aidRaw;
                if ($aid > 0) $uniq[$aid] = true;
            }

            foreach (array_keys($uniq) as $aid) {
                $ins->execute([':id_plat' => $platId, ':id_allergene' => $aid]);
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
