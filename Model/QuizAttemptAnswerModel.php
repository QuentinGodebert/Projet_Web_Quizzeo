<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';
function quizAttemptAnswersByAttempt(PDO $pdo, int $attemptId): array
{
    $sql = '
        SELECT qaa.*, q.intitule, q.type
        FROM quiz_attempt_answers qaa
        JOIN questions q ON q.id = qaa.question_id
        WHERE qaa.attempt_id = :attempt_id
        ORDER BY q.ordre ASC
    ';

    return dbFindAll($pdo, $sql, ['attempt_id' => $attemptId]);
}
function quizAttemptAnswerCreateChoice(
    PDO $pdo,
    int $attemptId,
    int $questionId,
    int $choiceId,
    ?bool $isCorrect
): int {
    $sql = '
        INSERT INTO quiz_attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct)
        VALUES (:attempt_id, :question_id, :choice_id, NULL, :is_correct)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'attempt_id' => $attemptId,
        'question_id'=> $questionId,
        'choice_id'  => $choiceId,
        'is_correct' => $isCorrect === null ? null : ($isCorrect ? 1 : 0),
    ]);

    return (int) $pdo->lastInsertId();
}
function quizAttemptAnswerCreateText(
    PDO $pdo,
    int $attemptId,
    int $questionId,
    string $answerText
): int {
    $sql = '
        INSERT INTO quiz_attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct)
        VALUES (:attempt_id, :question_id, NULL, :answer_text, NULL)
    ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'attempt_id' => $attemptId,
        'question_id'=> $questionId,
        'answer_text'=> $answerText,
    ]);

    return (int) $pdo->lastInsertId();
}
