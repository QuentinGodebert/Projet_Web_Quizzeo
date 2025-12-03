<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function questionFindById(int $id): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM questions WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    return $question ?: null;
}
function questionFindByQuiz(int $quizId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT *
        FROM questions
        WHERE quiz_id = :quiz_id
        ORDER BY ordre ASC
    ');
    $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function questionCreate(int $quizId, string $intitule, string $type, ?int $points, int $ordre): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO questions (quiz_id, intitule, type, points, ordre)
        VALUES (:quiz_id, :intitule, :type, :points, :ordre)
    ');

    $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    $stmt->bindValue(':intitule', $intitule, PDO::PARAM_STR);
    $stmt->bindValue(':type', $type, PDO::PARAM_STR);
    $stmt->bindValue(':points', $points, $points === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':ordre', $ordre, PDO::PARAM_INT);
    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
function questionUpdate(int $id, string $intitule, string $type, ?int $points, int $ordre): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE questions
        SET intitule = :intitule,
            type = :type,
            points = :points,
            ordre = :ordre
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':intitule', $intitule, PDO::PARAM_STR);
    $stmt->bindValue(':type', $type, PDO::PARAM_STR);
    $stmt->bindValue(':points', $points, $points === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(':ordre', $ordre, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function questionDelete(int $id): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('DELETE FROM questions WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
