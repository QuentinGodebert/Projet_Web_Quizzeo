<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function getQuestionsByQuizId(int $quizId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
    SELECT id, quiz_id, label, points, created_at, updated_at
    FROM questions
    WHERE quiz_id = :quiz_id
    ORDER BY id ASC
');
    $stmt->execute([':quiz_id' => $quizId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function createQuestion(int $quizId, string $label, int $points): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO questions (quiz_id, label, points, created_at, updated_at)
        VALUES (:quiz_id, :label, :points, NOW(), NOW())
    ');

    $ok = $stmt->execute([
        ':quiz_id' => $quizId,
        ':label'   => $label,
        ':points'  => $points,
    ]);

    if (!$ok) {
        return null;
    }

    return (int)$pdo->lastInsertId() ?: null;
}
function getQuestionById(int $id, int $quizId): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT id, quiz_id, label, points, created_at, updated_at
        FROM questions
        WHERE id = :id
          AND quiz_id = :quiz_id
        LIMIT 1
    ');
    $stmt->execute([
        ':id'      => $id,
        ':quiz_id' => $quizId,
    ]);

    $question = $stmt->fetch(PDO::FETCH_ASSOC);

    return $question ?: null;
}
function updateQuestion(int $id, int $quizId, string $label, int $points): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE questions
        SET label = :label,
            points = :points,
            updated_at = NOW()
        WHERE id = :id
          AND quiz_id = :quiz_id
    ');

    return $stmt->execute([
        ':label'   => $label,
        ':points'  => $points,
        ':id'      => $id,
        ':quiz_id' => $quizId,
    ]);
}
function deleteQuestion(int $id, int $quizId): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        DELETE FROM questions
        WHERE id = :id
          AND quiz_id = :quiz_id
    ');

    return $stmt->execute([
        ':id'      => $id,
        ':quiz_id' => $quizId,
    ]);
}
function questionFindByQuiz(int $quizId): array
{
    return getQuestionsByQuizId($quizId);
}
