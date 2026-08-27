<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = :email AND ativo = 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id_user = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare("SELECT id_user, name FROM user WHERE ativo = 1 ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(string $name, string $email, string $passwordHash): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO user (name, email, password, ativo) VALUES (:name, :email, :password, 1)"
        );
        $stmt->bindParam(':name',     $name);
        $stmt->bindParam(':email',    $email);
        $stmt->bindParam(':password', $passwordHash);
        return $stmt->execute();
    }
}
