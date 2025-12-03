<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function quizAttemptAnswersByAttempt(int $attemptId): array
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        SELECT qaa.*, q.intitule, q.type
        FROM quiz_attempt_answers qaa
        JOIN questions q ON q.id = qaa.question_id
        WHERE qaa.attempt_id = :attempt_id
        ORDER BY q.ordre ASC
    ');
    $stmt->bindValue(':attempt_id', $attemptId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
function quizAttemptAnswerCreateChoice(int $attemptId, int $questionId, int $choiceId, ?bool $isCorrect): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO quiz_attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct)
        VALUES (:attempt_id, :question_id, :choice_id, NULL, :is_correct)
    ');

    $stmt->bindValue(':attempt_id', $attemptId, PDO::PARAM_INT);
    $stmt->bindValue(':question_id', $questionId, PDO::PARAM_INT);
    $stmt->bindValue(':choice_id', $choiceId, PDO::PARAM_INT);

    if ($isCorrect === null) {
        $stmt->bindValue(':is_correct', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindValue(':is_correct', $isCorrect ? 1 : 0, PDO::PARAM_INT);
    }

    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
function quizAttemptAnswerCreateText(int $attemptId, int $questionId, string $answerText): ?int
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare('
        INSERT INTO quiz_attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct)
        VALUES (:attempt_id, :question_id, NULL, :answer_text, NULL)
    ');

    $stmt->bindValue(':attempt_id', $attemptId, PDO::PARAM_INT);
    $stmt->bindValue(':question_id', $questionId, PDO::PARAM_INT);
    $stmt->bindValue(':answer_text', $answerText, PDO::PARAM_STR);
    $stmt->execute();

    return (int) $pdo->lastInsertId() ?: null;
}
