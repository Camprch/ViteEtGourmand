<?php

// Fonctions principales :
// - findAll()         : Retourne tous les allergènes
// - findById(int $id) : Retourne un allergène par son ID
// - create(string $n) : Crée un nouvel allergène
// - update(int, str)  : Met à jour un allergène
// - delete(int)       : Supprime un allergène

declare(strict_types=1);

class AllergeneModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Retourne tous les allergènes
    public function findAll(): array
    {
        $sql = "SELECT id, nom FROM allergene ORDER BY nom ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // Retourne un allergène par son ID
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM allergene WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Crée un nouvel allergène
    public function create(string $nom): int
    {
        $stmt = $this->pdo->prepare("INSERT INTO allergene (nom) VALUES (:nom)");
        $stmt->execute([':nom' => $nom]);
        return (int)$this->pdo->lastInsertId();
    }

    // Met à jour un allergène
    public function update(int $id, string $nom): bool
    {
        $stmt = $this->pdo->prepare("UPDATE allergene SET nom = :nom WHERE id = :id");
        return $stmt->execute([':id' => $id, ':nom' => $nom]);
    }

    // Supprime un allergène
    public function delete(int $id): bool
    {
        // Si plat_allergene a ON DELETE RESTRICT sur allergene, ça peut échouer si utilisé
        $stmt = $this->pdo->prepare("DELETE FROM allergene WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
