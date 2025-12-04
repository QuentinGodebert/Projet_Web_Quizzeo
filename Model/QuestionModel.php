<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function getQuestionsByQuizId(int $quizId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT id,
               quiz_id,
               intitule,
               type,
               points,
               ordre
        FROM questions
        WHERE quiz_id = :quiz_id
        ORDER BY ordre ASC, id ASC
    ');
    $stmt->execute([':quiz_id' => $quizId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function createQuestion(int $quizId, string $intitule, int $points, string $type = 'qcm'): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO questions (quiz_id, intitule, type, points, ordre)
        VALUES (
            :quiz_id,
            :intitule,
            :type,
            :points,
            (
                SELECT IFNULL(MAX(q2.ordre), 0) + 1
                FROM questions q2
                WHERE q2.quiz_id = :quiz_id
            )
        )
    ');

    $ok = $stmt->execute([
        ':quiz_id'  => $quizId,
        ':intitule' => $intitule,
        ':type'     => $type,
        ':points'   => $points,
    ]);

    if (!$ok) {
        return null;
    }

    return (int)$pdo->lastInsertId() ?: null;
}

function getQuestionById(?int $id, ?int $quizId = null): ?array
{
    if ($id === null || $id <= 0) {
        return null;
    }

    $pdo = getDatabase();

    $sql = '
        SELECT id,
               quiz_id,
               intitule,
               type,
               points,
               ordre
        FROM questions
        WHERE id = :id
    ';

    if ($quizId !== null && $quizId > 0) {
        $sql .= ' AND quiz_id = :quiz_id';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($quizId !== null && $quizId > 0) {
        $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    }

    $stmt->execute();

    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    return $question ?: null;
}


function updateQuestion(int $id, int $quizId, string $intitule, int $points): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE questions
        SET intitule = :intitule,
            points    = :points
        WHERE id = :id
          AND quiz_id = :quiz_id
    ');

    return $stmt->execute([
        ':intitule' => $intitule,
        ':points'   => $points,
        ':id'       => $id,
        ':quiz_id'  => $quizId,
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
