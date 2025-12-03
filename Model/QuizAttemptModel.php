<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function quizAttemptFindById(int $id): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM quiz_attempts WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
    return $attempt ?: null;
}
function quizAttemptsByUser(int $userId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT qa.*, q.title
        FROM quiz_attempts qa
        JOIN quizzes q ON q.id = qa.quiz_id
        WHERE qa.user_id = :user_id
        ORDER BY qa.started_at DESC
    ');
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function quizAttemptsByQuiz(int $quizId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT qa.*, u.first_name, u.last_name
        FROM quiz_attempts qa
        JOIN users u ON u.id = qa.user_id
        WHERE qa.quiz_id = :quiz_id
        ORDER BY qa.started_at DESC
    ');
    $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function quizAttemptStart(int $quizId, int $userId): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO quiz_attempts (quiz_id, user_id, started_at, is_completed)
        VALUES (:quiz_id, :user_id, NOW(), 0)
    ');
    $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
function quizAttemptComplete(int $attemptId, float $score): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE quiz_attempts
        SET finished_at = NOW(),
            score = :score,
            is_completed = 1
        WHERE id = :id
    ');
    $stmt->bindValue(':id', $attemptId, PDO::PARAM_INT);
    $stmt->bindValue(':score', $score, PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
