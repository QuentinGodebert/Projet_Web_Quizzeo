<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function quizAttemptFindById(PDO $pdo, int $id): ?array
{
    $sql = 'SELECT * FROM quiz_attempts WHERE id = :id';
    return dbFindOne($pdo, $sql, ['id' => $id]);
}
function quizAttemptsByUser(PDO $pdo, int $userId): array
{
    $sql = '
        SELECT qa.*, q.title
        FROM quiz_attempts qa
        JOIN quizzes q ON q.id = qa.quiz_id
        WHERE qa.user_id = :user_id
        ORDER BY qa.started_at DESC
    ';

    return dbFindAll($pdo, $sql, ['user_id' => $userId]);
}
function quizAttemptsByQuiz(PDO $pdo, int $quizId): array
{
    $sql = '
        SELECT qa.*, u.first_name, u.last_name
        FROM quiz_attempts qa
        JOIN users u ON u.id = qa.user_id
        WHERE qa.quiz_id = :quiz_id
        ORDER BY qa.started_at DESC
    ';

    return dbFindAll($pdo, $sql, ['quiz_id' => $quizId]);
}
function quizAttemptStart(PDO $pdo, int $quizId, int $userId): int
{
    $sql = '
        INSERT INTO quiz_attempts (quiz_id, user_id, started_at, is_completed)
        VALUES (:quiz_id, :user_id, NOW(), 0)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'quiz_id' => $quizId,
        'user_id' => $userId,
    ]);

    return (int) $pdo->lastInsertId();
}
function quizAttemptComplete(
    PDO $pdo,
    int $attemptId,
    float $score
): int {
    $sql = '
        UPDATE quiz_attempts
        SET finished_at = NOW(),
            score = :score,
            is_completed = 1
        WHERE id = :id
    ';

    return dbExecute($pdo, $sql, [
        'id'    => $attemptId,
        'score' => $score,
    ]);
}
