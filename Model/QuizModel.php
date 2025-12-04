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
function createQuiz(PDO $pdo, string $title, string $description, int $owner_id): bool
{
    $stmt = $pdo->prepare("
        INSERT INTO quizzes (title, description, owner_id, is_active, created_at, updated_at)
        VALUES (:title, :description, :owner_id, 1, NOW(), NOW())
    ");
    return $stmt->execute([
        'title'       => $title,
        'description' => $description,
        'owner_id'    => $owner_id,
    ]);
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
function getAllQuizzes(): array
{
    $pdo = getDatabase();
    $stmt = $pdo->query("SELECT id, title, status, is_active, created_at FROM quizzes ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function toggleQuizStatus(int $id): void
{
    $pdo = getDatabase();
    $pdo->prepare("UPDATE quizzes SET is_active = NOT is_active WHERE id = ?")->execute([$id]);
} function quizCreate(int $ownerId, string $title, ?string $description): bool
{
    $pdo = getDatabase();


    return createQuiz($pdo, $title, $description ?? '', $ownerId);
}

