<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function choiceFindById(int $id): ?array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM choices WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $choice = $stmt->fetch(PDO::FETCH_ASSOC);
    return $choice ?: null;
}
function choiceFindByQuestion(int $questionId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT *
        FROM choices
        WHERE question_id = :question_id
        ORDER BY ordre ASC
    ');
    $stmt->bindValue(':question_id', $questionId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function choiceCreate(int $questionId, string $libelle, bool $isCorrect, int $ordre): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO choices (question_id, libelle, is_correct, ordre)
        VALUES (:question_id, :libelle, :is_correct, :ordre)
    ');

    $stmt->bindValue(':question_id', $questionId, PDO::PARAM_INT);
    $stmt->bindValue(':libelle', $libelle, PDO::PARAM_STR);
    $stmt->bindValue(':is_correct', $isCorrect ? 1 : 0, PDO::PARAM_INT);
    $stmt->bindValue(':ordre', $ordre, PDO::PARAM_INT);
    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
function choiceUpdate(int $id, string $libelle, bool $isCorrect, int $ordre): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        UPDATE choices
        SET libelle = :libelle,
            is_correct = :is_correct,
            ordre = :ordre
        WHERE id = :id
    ');

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':libelle', $libelle, PDO::PARAM_STR);
    $stmt->bindValue(':is_correct', $isCorrect ? 1 : 0, PDO::PARAM_INT);
    $stmt->bindValue(':ordre', $ordre, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
function choiceDelete(int $id): bool
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('DELETE FROM choices WHERE id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}
