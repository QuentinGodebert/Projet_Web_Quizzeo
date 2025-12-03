<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function userFindById(PDO $pdo, int $id): ?array
{
    $sql = 'SELECT * FROM users WHERE id = :id';
    return dbFindOne($pdo, $sql, ['id' => $id]);
}

function userFindByEmail(PDO $pdo, string $email): ?array
{
    $sql = 'SELECT * FROM users WHERE email = :email';
    return dbFindOne($pdo, $sql, ['email' => $email]);
}

function userAll(PDO $pdo): array
{
    $sql = 'SELECT * FROM users ORDER BY created_at DESC';
    return dbFindAll($pdo, $sql);
}

function userAllByRole(PDO $pdo, string $role): array
{
    $sql = 'SELECT * FROM users WHERE role = :role ORDER BY created_at DESC';
    return dbFindAll($pdo, $sql, ['role' => $role]);
}
function userCreate(
    PDO $pdo,
    string $role,
    string $email,
    string $password,
    string $firstName,
    string $lastName
): int {
    $sql = '
        INSERT INTO users (role, email, password, first_name, last_name, is_active, created_at, updated_at)
        VALUES (:role, :email, :password, :first_name, :last_name, 1, NOW(), NOW())
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'role'       => $role,
        'email'      => $email,
        'password'   => password_hash($password, PASSWORD_BCRYPT),
        'first_name' => $firstName,
        'last_name'  => $lastName,
    ]);

    return (int) $pdo->lastInsertId();
}
function userUpdateProfile(
    PDO $pdo,
    int $id,
    string $email,
    string $firstName,
    string $lastName
): int {
    $sql = '
        UPDATE users
        SET email = :email,
            first_name = :first_name,
            last_name = :last_name,
            updated_at = NOW()
        WHERE id = :id
    ';

    return dbExecute($pdo, $sql, [
        'id'         => $id,
        'email'      => $email,
        'first_name' => $firstName,
        'last_name'  => $lastName,
    ]);
}
function userUpdatePassword(PDO $pdo, int $id, string $newPassword): int
{
    $sql = '
        UPDATE users
        SET password = :password,
            updated_at = NOW()
        WHERE id = :id
    ';

    return dbExecute($pdo, $sql, [
        'id'       => $id,
        'password' => password_hash($newPassword, PASSWORD_BCRYPT),
    ]);
}
function userSetActive(PDO $pdo, int $id, bool $isActive): int
{
    $sql = '
        UPDATE users
        SET is_active = :is_active,
            updated_at = NOW()
        WHERE id = :id
    ';

    return dbExecute($pdo, $sql, [
        'id'        => $id,
        'is_active' => $isActive ? 1 : 0,
    ]);
}
require_once __DIR__ . '/../config/database.php';
function findUserByEmail($email)
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    return $user ?: null;
}
