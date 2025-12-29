<?php

// Modèle pour la gestion des utilisateurs (clients et employés).

// - findByEmail : récupère un utilisateur par email
// - create : ajoute un utilisateur
// - createPasswordResetToken : crée un token de réinitialisation de mot de passe
// - findValidPasswordResetToken : vérifie un token de réinitialisation
// - markPasswordResetTokenUsed : marque un token comme utilisé
// - updatePassword : met à jour le mot de passe
// - findAllEmployes : liste tous les employés
// - setActif : active/désactive un employé
// - findById : récupère un utilisateur par id
// - emailExists : vérifie l'existence d'un email (hors id donné)
// - updateProfile : met à jour le profil d'un utilisateur
// - getPasswordHash : récupère le hash du mot de passe

declare(strict_types=1);

class UserModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Récupère un utilisateur par son email (ou null si non trouvé)
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM `user` WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Crée un nouvel utilisateur et retourne son id
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
            ':telephone'  => $data['telephone'] ?? null,
            ':adresse'    => $data['adresse'] ?? null,
            ':role'       => (string)$data['role'],
            ':actif'      => (int)$data['actif'],
            ':created_at' => (string)$data['created_at'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    // Crée un token de réinitialisation de mot de passe pour un utilisateur
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

    // Vérifie la validité d'un token de réinitialisation (non utilisé et non expiré)
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
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Marque un token de réinitialisation comme utilisé
    public function markPasswordResetTokenUsed(int $tokenId, string $usedAt): void
    {
        // Si ta table n'a pas de used_at, on ignore $usedAt et on met juste used=1
        $sql = "UPDATE password_reset_token SET used = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $tokenId]);
    }

    // Met à jour le mot de passe d'un utilisateur
    public function updatePassword(int $userId, string $passwordHash): void
    {
        $sql = "UPDATE `user` SET password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':password' => $passwordHash,
            ':id' => $userId
        ]);
    }

    // Récupère la liste de tous les employés
    public function findAllEmployes(): array
    {
        $sql = "SELECT id, nom, prenom, email, role, actif, created_at
                FROM `user`
                WHERE role = 'EMPLOYE'
                ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Active ou désactive un employé
    public function setActif(int $userId, int $actif): void
    {
        $sql = "UPDATE `user` SET actif = :actif WHERE id = :id AND role = 'EMPLOYE'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':actif' => $actif,
            ':id' => $userId,
        ]);
    }

    // Récupère un utilisateur par son identifiant (ou null si non trouvé)
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nom, prenom, email, telephone, adresse, role, actif, created_at
                FROM `user`
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Vérifie si un email existe déjà (hors utilisateur donné)
    public function emailExists(string $email, int $excludeId): bool
    {
        $sql = "SELECT COUNT(*) FROM `user` WHERE email = :email AND id != :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email, ':id' => $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // Met à jour les informations du profil d'un utilisateur
    public function updateProfile(int $id, array $data): bool
    {
        $sql = "UPDATE `user` SET
                    nom = :nom,
                    prenom = :prenom,
                    email = :email,
                    telephone = :telephone,
                    adresse = :adresse
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nom', $data['nom'], PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $data['prenom'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);

        if ($data['telephone'] === null) {
            $stmt->bindValue(':telephone', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':telephone', $data['telephone'], PDO::PARAM_STR);
        }

        if ($data['adresse'] === null) {
            $stmt->bindValue(':adresse', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':adresse', $data['adresse'], PDO::PARAM_STR);
        }

        return $stmt->execute();
    }

    // Récupère le hash du mot de passe d'un utilisateur
    public function getPasswordHash(int $userId): string
    {
        $sql = "SELECT password FROM `user` WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return (string)$stmt->fetchColumn();
    }
}
