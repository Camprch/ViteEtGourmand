<?php
declare(strict_types=1);

class AuthController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function showRegisterForm(): void
    {
        require __DIR__ . '/../../views/auth/register.php';
    }
}
