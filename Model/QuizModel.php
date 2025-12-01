<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function quiz_find_by_id(PDO $pdo, int $id): ?array
{
    $sql = 'SELECT * FROM quizzes WHERE id = :id';
    return db_find_one($pdo, $sql, ['id' => $id]);
}

function quiz_find_by_access_token(PDO $pdo, string $token): ?array
{
    $sql = 'SELECT * FROM quizzes WHERE access_token = :token';
    return db_find_one($pdo, $sql, ['token' => $token]);
}

function quiz_find_by_owner(PDO $pdo, int $ownerId): array
{
    $sql = '
        SELECT *
        FROM quizzes
        WHERE owner_id = :owner_id
        ORDER BY created_at DESC
    ';

    return db_find_all($pdo, $sql, ['owner_id' => $ownerId]);
}

function quiz_all(PDO $pdo): array
{
    $sql = '
        SELECT q.*, u.first_name, u.last_name, u.role
        FROM quizzes q
        JOIN users u ON u.id = q.owner_id
        ORDER BY q.created_at DESC
    ';

    return db_find_all($pdo, $sql);
}

function quiz_create(
    PDO $pdo,
    int $ownerId,
    string $title,
    ?string $description
): int {
    $accessToken = bin2hex(random_bytes(32));

    $sql = '
        INSERT INTO quizzes (owner_id, title, description, status, is_active, access_token, created_at, updated_at)
        VALUES (:owner_id, :title, :description, :status, 1, :access_token, NOW(), NOW())
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'owner_id'     => $ownerId,
        'title'        => $title,
        'description'  => $description,
        'status'       => 'draft',
        'access_token' => $accessToken,
    ]);

    return (int) $pdo->lastInsertId();
}

function quiz_update(
    PDO $pdo,
    int $id,
    string $title,
    ?string $description
): int {
    $sql = '
        UPDATE quizzes
        SET title = :title,
            description = :description,
            updated_at = NOW()
        WHERE id = :id
    ';

    return db_execute($pdo, $sql, [
        'id'          => $id,
        'title'       => $title,
        'description' => $description,
    ]);
}

function quiz_set_status(PDO $pdo, int $id, string $status): int
{
    $allowed = ['draft', 'launched', 'finished'];
    if (!in_array($status, $allowed, true)) {
        throw new InvalidArgumentException('Statut de quiz invalide.');
    }

    $sql = '
        UPDATE quizzes
        SET status = :status,
            updated_at = NOW()
        WHERE id = :id
    ';

    return db_execute($pdo, $sql, [
        'id'     => $id,
        'status' => $status,
    ]);
}

function quiz_set_active(PDO $pdo, int $id, bool $isActive): int
{
    $sql = '
        UPDATE quizzes
        SET is_active = :is_active,
            updated_at = NOW()
        WHERE id = :id
    ';

    return db_execute($pdo, $sql, [
        'id'        => $id,
        'is_active' => $isActive ? 1 : 0,
    ]);
}
