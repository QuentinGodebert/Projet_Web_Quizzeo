<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function userFindById(int $id): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}
function userFindByEmail(string $email): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
}
function userAll(): array
{
    $pdo = getDatabase();

    $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function userAllByRole(string $role): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM users WHERE role = :role ORDER BY created_at DESC');
    $stmt->bindValue(':role', $role, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function userCreate(string $role, string $email, string $password, string $firstName, string $lastName): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO users (role, email, password, first_name, last_name, is_active, created_at, updated_at)
        VALUES (:role, :email, :password, :first_name, :last_name, 1, NOW(), NOW())
    ');

    $stmt->bindValue(':role', $role, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
    $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $lastName, PDO::PARAM_STR);
    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
function userUpdateProfile(int $id, string $email, string $firstName, string $lastName): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE users
        SET email = :email,
            first_name = :first_name,
            last_name = :last_name,
            updated_at = NOW()
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindValue(':last_name', $lastName, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function userUpdatePassword(int $id, string $newPassword): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE users
        SET password = :password,
            updated_at = NOW()
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':password', password_hash($newPassword, PASSWORD_BCRYPT), PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function userSetActive(int $id, bool $isActive): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE users
        SET is_active = :is_active,
            updated_at = NOW()
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':is_active', $isActive ? 1 : 0, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function getAllUsers(): array
{
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, role, is_active FROM users ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function toggleUserStatus(int $id): void
{
    $pdo = getDatabase();
    $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
}
