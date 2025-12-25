<?php
declare(strict_types=1);

class ContactModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO contact_message (nom, email, titre, message, date, traite)
                VALUES (:nom, :email, :titre, :message, :date, :traite)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => (string)$data['nom'],
            ':email' => (string)$data['email'],
            ':titre' => (string)$data['titre'],
            ':message' => (string)$data['message'],
            ':date' => (string)$data['date'],
            ':traite' => (int)$data['traite'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}
