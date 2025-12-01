<?php

<<<<<<< HEAD
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function user_find_by_id(PDO $pdo, int $id): ?array
{
    $sql = 'SELECT * FROM users WHERE id = :id';
    return db_find_one($pdo, $sql, ['id' => $id]);
}

function user_find_by_email(PDO $pdo, string $email): ?array
{
    $sql = 'SELECT * FROM users WHERE email = :email';
    return db_find_one($pdo, $sql, ['email' => $email]);
}

function user_all(PDO $pdo): array
{
    $sql = 'SELECT * FROM users ORDER BY created_at DESC';
    return db_find_all($pdo, $sql);
}

function user_all_by_role(PDO $pdo, string $role): array
{
    $sql = 'SELECT * FROM users WHERE role = :role ORDER BY created_at DESC';
    return db_find_all($pdo, $sql, ['role' => $role]);
}
function user_create(
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
function user_update_profile(
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

    return db_execute($pdo, $sql, [
        'id'         => $id,
        'email'      => $email,
        'first_name' => $firstName,
        'last_name'  => $lastName,
    ]);
}
function user_update_password(PDO $pdo, int $id, string $newPassword): int
{
    $sql = '
        UPDATE users
        SET password = :password,
            updated_at = NOW()
        WHERE id = :id
    ';

    return db_execute($pdo, $sql, [
        'id'       => $id,
        'password' => password_hash($newPassword, PASSWORD_BCRYPT),
    ]);
}
function user_set_active(PDO $pdo, int $id, bool $isActive): int
{
    $sql = '
        UPDATE users
        SET is_active = :is_active,
            updated_at = NOW()
        WHERE id = :id
    ';

    return db_execute($pdo, $sql, [
        'id'        => $id,
        'is_active' => $isActive ? 1 : 0,
    ]);
=======
require_once __DIR__ . '/../config/database.php';

/**
 * Trouver un utilisateur par son email.
 *
 * @param string $email L'email de l'utilisateur à rechercher.
 * @return array|null Les données de l'utilisateur ou null s'il n'existe pas.
 */
function findUserByEmail($email)
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    return $user ?: null;
>>>>>>> 9b90afa6a7c99294b3b0d95c16b5660a14fc764e
}
