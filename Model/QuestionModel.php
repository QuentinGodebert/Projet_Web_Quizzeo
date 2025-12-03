<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function questionFindById(PDO $pdo, int $id): ?array
{
    $sql = 'SELECT * FROM questions WHERE id = :id';
    return dbFindOne($pdo, $sql, ['id' => $id]);
}
function questionFindByQuiz(PDO $pdo, int $quizId): array
{
    $sql = '
        SELECT *
        FROM questions
        WHERE quiz_id = :quiz_id
        ORDER BY ordre ASC
    ';

    return dbFindAll($pdo, $sql, ['quiz_id' => $quizId]);
}
function questionCreate(
    PDO $pdo,
    int $quizId,
    string $intitule,
    string $type,
    ?int $points,
    int $ordre
): int {
    $sql = '
        INSERT INTO questions (quiz_id, intitule, type, points, ordre)
        VALUES (:quiz_id, :intitule, :type, :points, :ordre)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'quiz_id'  => $quizId,
        'intitule' => $intitule,
        'type'     => $type,
        'points'   => $points,
        'ordre'    => $ordre,
    ]);

    return (int) $pdo->lastInsertId();
}
function questionUpdate(
    PDO $pdo,
    int $id,
    string $intitule,
    string $type,
    ?int $points,
    int $ordre
): int {
    $sql = '
        UPDATE questions
        SET intitule = :intitule,
            type = :type,
            points = :points,
            ordre = :ordre
        WHERE id = :id
    ';

    return dbExecute($pdo, $sql, [
        'id'       => $id,
        'intitule' => $intitule,
        'type'     => $type,
        'points'   => $points,
        'ordre'    => $ordre,
    ]);
}
function questionDelete(PDO $pdo, int $id): int
{
    $sql = 'DELETE FROM questions WHERE id = :id';
    return dbExecute($pdo, $sql, ['id' => $id]);
}
