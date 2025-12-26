<?php
declare(strict_types=1);

class PlatModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, nom, description, type
                FROM plat
                ORDER BY type ASC, nom ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nom, description, type FROM plat WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

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

    public function delete(int $id): bool
    {
        // OK car menu_plat et plat_allergene ont ON DELETE CASCADE côté plat
        $stmt = $this->pdo->prepare("DELETE FROM plat WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
