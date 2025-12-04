<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function getQuestionsByQuizId(int $quizId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT *
        FROM questions
        WHERE quiz_id = :quiz_id
        ORDER BY ordre ASC, id ASC
    ');
    $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function getQuestionById(int $id, int $quizId): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT *
        FROM questions
        WHERE id = :id AND quiz_id = :quiz_id
    ');
    $stmt->execute([
        ':id' => $id,
        ':quiz_id' => $quizId
    ]);

    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    return $question ?: null;
}
function createQuestion(int $quizId, string $intitule, int $points): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO questions (quiz_id, intitule, type, points, ordre)
        VALUES (:quiz_id, :intitule, "qcm", :points, (
            SELECT COALESCE(MAX(ordre), 0) + 1 FROM questions WHERE quiz_id = :quiz_id_ordre
        ))
    ');
    $stmt->execute([
        ':quiz_id' => $quizId,
        ':intitule' => $intitule,
        ':points' => $points,
        ':quiz_id_ordre' => $quizId
    ]);

    return (int) $pdo->lastInsertId() ?: null;
}
function updateQuestion(int $id, int $quizId, string $intitule, int $points): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE questions
        SET intitule = :intitule,
            points = :points
        WHERE id = :id AND quiz_id = :quiz_id
    ');
    $stmt->execute([
        ':id' => $id,
        ':quiz_id' => $quizId,
        ':intitule' => $intitule,
        ':points' => $points
    ]);

    return $stmt->rowCount() > 0;
}
function deleteQuestion(int $id, int $quizId): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('DELETE FROM questions WHERE id = :id AND quiz_id = :quiz_id');
    $stmt->execute([':id' => $id, ':quiz_id' => $quizId]);

    return $stmt->rowCount() > 0;
}
