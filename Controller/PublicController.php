<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function requireQuizUserLogin(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
}
function publicHomeController(): void
{
    require __DIR__ . '/../View/public/home.php';
}
function publicStartQuizController(): void
{
    requireQuizUserLogin();

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    if ($method === 'GET') {
        $token = $_GET['token'] ?? '';
    } else {
        $token = $_POST['token'] ?? '';
    }

    if ($token === '') {
        http_response_code(404);
        echo 'Quiz introuvable.';
        return;
    }

    $pdo = getDatabase();

    $quiz = findQuizByAccessToken($pdo, $token);
    if (!$quiz) {
        http_response_code(404);
        echo 'Quiz introuvable.';
        return;
    }

    if ((int)$quiz['is_active'] !== 1 || $quiz['status'] !== 'launched') {
        http_response_code(403);
        echo 'Ce quiz n\'est pas disponible.';
        return;
    }

    $questions = findQuestionsWithChoices($pdo, (int)$quiz['id']);

    if ($method === 'GET') {
        require __DIR__ . '/../View/quiz/start_quiz.php';
        return;
    }
    $answersForm = $_POST['answers'] ?? [];

    $score = 0.0;
    $maxScore = 0.0;

    foreach ($questions as $question) {
        $questionId = (int)$question['id'];
        $type = $question['type'];

        if ($type === 'qcm') {
            $correctChoiceIds = [];
            foreach ($question['choices'] as $choice) {
                if ((int)$choice['is_correct'] === 1) {
                    $correctChoiceIds[] = (int)$choice['id'];
                }
            }
            sort($correctChoiceIds);

            $userAnswerIds = $answersForm[$questionId] ?? [];
            if (!is_array($userAnswerIds)) {
                $userAnswerIds = [$userAnswerIds];
            }
            $userAnswerIds = array_map('intval', $userAnswerIds);
            sort($userAnswerIds);

            $maxScore++;

            if ($correctChoiceIds === $userAnswerIds) {
                $score++;
            }
        } else {
            $maxScore++;
        }
    }

    $userId = (int)($_SESSION['user']['id'] ?? 0);

    if ($userId <= 0) {
        http_response_code(500);
        echo 'Utilisateur non valide pour enregistrer la tentative.';
        return;
    }

    $now = date('Y-m-d H:i:s');

    $pdo->beginTransaction();

    try {
  
        $stmtAttempt = $pdo->prepare('
            INSERT INTO quiz_attempts (quiz_id, user_id, started_at, finished_at, score, is_completed)
            VALUES (:quiz_id, :user_id, :started_at, :finished_at, :score, :is_completed)
        ');
        $stmtAttempt->execute([
            ':quiz_id'      => (int)$quiz['id'],
            ':user_id'      => $userId,
            ':started_at'   => $now,
            ':finished_at'  => $now,
            ':score'        => $score,
            ':is_completed' => 1,
        ]);

        $attemptId = (int)$pdo->lastInsertId();

        $stmtAnswer = $pdo->prepare('
            INSERT INTO quiz_attempt_answers (attempt_id, question_id, choice_id, answer_text, is_correct)
            VALUES (:attempt_id, :question_id, :choice_id, :answer_text, :is_correct)
        ');

        foreach ($questions as $question) {
            $questionId = (int)$question['id'];
            $type = $question['type'];
            $userValue = $answersForm[$questionId] ?? null;

            if ($type === 'qcm') {
                $correctChoiceIds = [];
                foreach ($question['choices'] as $choice) {
                    if ((int)$choice['is_correct'] === 1) {
                        $correctChoiceIds[] = (int)$choice['id'];
                    }
                }
                sort($correctChoiceIds);

                $userChoiceIds = $userValue ?? [];
                if (!is_array($userChoiceIds)) {
                    $userChoiceIds = [$userChoiceIds];
                }
                $userChoiceIds = array_map('intval', $userChoiceIds);
                sort($userChoiceIds);

                $questionIsCorrect = ($correctChoiceIds === $userChoiceIds);
                $isCorrectFlag = $questionIsCorrect ? 1 : 0;

                foreach ($userChoiceIds as $choiceId) {
                    $stmtAnswer->execute([
                        ':attempt_id'  => $attemptId,
                        ':question_id' => $questionId,
                        ':choice_id'   => $choiceId,
                        ':answer_text' => null,
                        ':is_correct'  => $isCorrectFlag,
                    ]);
                }
            } else {
             
                $answerText = is_string($userValue) ? trim($userValue) : '';

                $stmtAnswer->execute([
                    ':attempt_id'  => $attemptId,
                    ':question_id' => $questionId,
                    ':choice_id'   => null,
                    ':answer_text' => $answerText,
                    ':is_correct'  => null,
                ]);
            }
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo 'Erreur lors de l\'enregistrement de vos réponses.';
        return;
    }
    require __DIR__ . '/../View/quiz/end_quiz.php';
}
function findQuizByAccessToken(PDO $pdo, string $token): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM quizzes WHERE access_token = :token LIMIT 1');
    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    return $quiz ?: null;
}
function findQuestionsWithChoices(PDO $pdo, int $quizId): array
{
    $stmt = $pdo->prepare('
        SELECT *
        FROM questions
        WHERE quiz_id = :quiz_id
        ORDER BY ordre ASC, id ASC
    ');
    $stmt->bindValue(':quiz_id', $quizId, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        return [];
    }

    $questionIds = array_column($questions, 'id');

    $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
    $stmtChoices = $pdo->prepare("
        SELECT *
        FROM choices
        WHERE question_id IN ($placeholders)
        ORDER BY question_id ASC, ordre ASC, id ASC
    ");

    foreach ($questionIds as $index => $id) {
        $stmtChoices->bindValue($index + 1, $id, PDO::PARAM_INT);
    }

    $stmtChoices->execute();
    $choices = $stmtChoices->fetchAll(PDO::FETCH_ASSOC);

    $choicesByQuestion = [];
    foreach ($choices as $choice) {
        $qid = (int)$choice['question_id'];
        $choicesByQuestion[$qid][] = $choice;
    }

    foreach ($questions as &$question) {
        $qid = (int)$question['id'];
        $question['choices'] = $choicesByQuestion[$qid] ?? [];
    }
    unset($question);

    return $questions;
}
