<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function quizFindById(int $id): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM quizzes WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    return $quiz ?: null;
}
function quizFindByAccessToken(string $token): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM quizzes WHERE access_token = :token');
    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    return $quiz ?: null;
}
function quizFindByOwner(int $ownerId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT *
        FROM quizzes
        WHERE owner_id = :owner_id
        ORDER BY created_at DESC
    ');
    $stmt->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function quizAll(): array
{
    $pdo = getDatabase();

    $stmt = $pdo->query('
        SELECT q.*, u.first_name, u.last_name, u.role
        FROM quizzes q
        JOIN users u ON u.id = q.owner_id
        ORDER BY q.created_at DESC
    ');

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function quizCreate(int $ownerId, string $title, ?string $description): ?int
{
    $pdo = getDatabase();

    $accessToken = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare('
        INSERT INTO quizzes (owner_id, title, description, status, is_active, access_token, created_at, updated_at)
        VALUES (:owner_id, :title, :description, :status, 1, :access_token, NOW(), NOW())
    ');

    $stmt->bindValue(':owner_id', $ownerId, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':status', 'draft', PDO::PARAM_STR);
    $stmt->bindValue(':access_token', $accessToken, PDO::PARAM_STR);
    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
function quizUpdate(int $id, string $title, ?string $description): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE quizzes
        SET title = :title,
            description = :description,
            updated_at = NOW()
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function quizSetStatus(int $id, string $status): bool
{
    $allowed = ['draft', 'launched', 'finished'];
    if (!in_array($status, $allowed, true)) {
        throw new InvalidArgumentException('Statut de quiz invalide.');
    }

    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE quizzes
        SET status = :status,
            updated_at = NOW()
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function quizSetActive(int $id, bool $isActive): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE quizzes
        SET is_active = :is_active,
            updated_at = NOW()
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':is_active', $isActive ? 1 : 0, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
