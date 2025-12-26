<?php
declare(strict_types=1);

class AllergeneModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $sql = "SELECT id, nom FROM allergene ORDER BY nom ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM allergene WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $nom): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO allergene (nom) VALUES (:nom)");
        $stmt->execute([':nom' => $nom]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $nom): bool
    {
        $stmt = $this->pdo->prepare("UPDATE allergene SET nom = :nom WHERE id = :id");
        return $stmt->execute([':id' => $id, ':nom' => $nom]);
    }

    public function delete(int $id): bool
    {
        // Si plat_allergene a ON DELETE RESTRICT sur allergene, ça peut échouer si utilisé
        $stmt = $this->pdo->prepare("DELETE FROM allergene WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
