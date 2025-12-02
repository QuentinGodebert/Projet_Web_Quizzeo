<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function choice_find_by_id(PDO $pdo, int $id): ?array
{
    $sql = 'SELECT * FROM choices WHERE id = :id';
    return db_find_one($pdo, $sql, ['id' => $id]);
}

function choice_find_by_question(PDO $pdo, int $questionId): array
{
    $sql = '
        SELECT *
        FROM choices
        WHERE question_id = :question_id
        ORDER BY ordre ASC
    ';

    return db_find_all($pdo, $sql, ['question_id' => $questionId]);
}
function choice_create(
    PDO $pdo,
    int $questionId,
    string $libelle,
    bool $isCorrect,
    int $ordre
): int {
    $sql = '
        INSERT INTO choices (question_id, libelle, is_correct, ordre)
        VALUES (:question_id, :libelle, :is_correct, :ordre)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'question_id' => $questionId,
        'libelle'     => $libelle,
        'is_correct'  => $isCorrect ? 1 : 0,
        'ordre'       => $ordre,
    ]);

    return (int) $pdo->lastInsertId();
}
function choice_update(
    PDO $pdo,
    int $id,
    string $libelle,
    bool $isCorrect,
    int $ordre
): int {
    $sql = '
        UPDATE choices
        SET libelle = :libelle,
            is_correct = :is_correct,
            ordre = :ordre
        WHERE id = :id
    ';

    return db_execute($pdo, $sql, [
        'id'         => $id,
        'libelle'    => $libelle,
        'is_correct' => $isCorrect ? 1 : 0,
        'ordre'      => $ordre,
    ]);
}
function choice_delete(PDO $pdo, int $id): int
{
    $sql = 'DELETE FROM choices WHERE id = :id';
    return db_execute($pdo, $sql, ['id' => $id]);
}