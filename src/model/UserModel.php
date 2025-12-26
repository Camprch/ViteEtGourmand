<?php
declare(strict_types=1);

class UserModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM `user` WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO `user` (nom, prenom, email, password, telephone, adresse, role, actif, created_at)
            VALUES (:nom, :prenom, :email, :password, :telephone, :adresse, :role, :actif, :created_at)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'        => (string)$data['nom'],
            ':prenom'     => (string)$data['prenom'],
            ':email'      => (string)$data['email'],
            ':password'   => (string)$data['password'],
            ':telephone'  => (string)($data['telephone'] ?? ''),
            ':adresse'    => (string)($data['adresse'] ?? ''),
            ':role'       => (string)$data['role'],
            ':actif'      => (int)$data['actif'],
            ':created_at' => (string)$data['created_at'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createPasswordResetToken(int $userId, string $token): void
    {
        // Optionnel : invalider les anciens tokens non utilisés de cet utilisateur
        $sql = "UPDATE password_reset_token
                SET used = 1
                WHERE id_user = :id_user AND used = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_user' => $userId]);

        $sql = "INSERT INTO password_reset_token (id_user, token, expires_at, used)
                VALUES (:id_user, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR), 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_user' => $userId,
            ':token' => $token,
        ]);

            }

            public function findValidPasswordResetToken(string $token): ?array
            {
                $sql = "SELECT id, id_user, token, expires_at, used
                FROM password_reset_token
                WHERE token = :token
                AND used = 0
                AND expires_at >= NOW()
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function markPasswordResetTokenUsed(int $tokenId, string $usedAt): void
    {
        // Si ta table n'a pas de used_at, on ignore $usedAt et on met juste used=1
        $sql = "UPDATE password_reset_token SET used = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $tokenId]);
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $sql = "UPDATE `user` SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':password' => $passwordHash,
            ':id' => $userId
        ]);
    }

    public function findAllEmployes(): array
{
    $sql = "SELECT id, nom, prenom, email, role, actif, created_at
            FROM `user`
            WHERE role = 'EMPLOYE'
            ORDER BY created_at DESC";
    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll();
}

public function setActif(int $userId, int $actif): void
{
    $sql = "UPDATE `user` SET actif = :actif WHERE id = :id AND role = 'EMPLOYE'";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':actif' => $actif,
        ':id' => $userId,
    ]);
}

}
