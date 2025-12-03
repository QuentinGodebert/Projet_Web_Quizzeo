<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/csrf.php';

require_once __DIR__ . '/../Model/QuizModel.php';
require_once __DIR__ . '/../Model/QuestionModel.php';
require_once __DIR__ . '/../Model/AnswerModel.php';
require_once __DIR__ . '/../Model/QuizAttemptModel.php';
require_once __DIR__ . '/../Model/QuizAttemptAnswerModel.php';
require_once __DIR__ . '/../config/database.php';

function quizStartController(): void
{
    if (!isset($_GET['token'])) {
        http_response_code(400);
        echo "Token de quiz manquant.";
        return;
    }

    $token = $_GET['token'];
    $quiz = quizFindByAccessToken($token);

    if (!$quiz || !$quiz['is_active'] || $quiz['status'] !== 'launched') {
        http_response_code(404);
        echo "Quiz introuvable.";
        return;
    }

    $questions = questionFindByQuiz((int) $quiz['id']);

    $choicesByQuestion = [];
    foreach ($questions as $question) {
        if ($question['type'] === 'qcm') {
            $choicesByQuestion[$question['id']] = choiceFindByQuestion((int) $question['id']);
        }
    }

    require __DIR__ . '/../View/quiz/start_quiz.php';
}
function quizSubmitController(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo "Méthode non autorisée.";
        return;
    }

    validate_csrf_or_die();

    if (!isset($_POST['token'])) {
        http_response_code(400);
        echo "Token de quiz manquant.";
        return;
    }

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
        http_response_code(403);
        echo "Vous devez être connecté avec un compte utilisateur pour répondre.";
        return;
    }

    $token = $_POST['token'];
    $quiz = quizFindByAccessToken($token);

    if (!$quiz || !$quiz['is_active'] || $quiz['status'] !== 'launched') {
        http_response_code(400);
        echo "Quiz introuvable ou non disponible.";
        return;
    }

    $userId = (int) $_SESSION['user']['id'];
    $quizId = (int) $quiz['id'];
    $questions = questionFindByQuiz($quizId);

    if (empty($questions)) {
        http_response_code(400);
        echo "Aucune question trouvée pour ce quiz.";
        return;
    }

    $attemptId = quizAttemptStart($quizId, $userId);

    $totalPoints = 0;
    $maxPoints = 0;

    foreach ($questions as $question) {
        $questionId = (int) $question['id'];
        $type = $question['type'];
        $points = (int) ($question['points'] ?? 0);
        $maxPoints += max(0, $points);

        $fieldName = 'question_' . $questionId;

        if ($type === 'qcm') {
            $choiceId = isset($_POST[$fieldName]) ? (int) $_POST[$fieldName] : 0;
            $isCorrect = null;

            if ($choiceId > 0) {
                $choice = choiceFindById($choiceId);
                if ($choice && (int) $choice['question_id'] === $questionId) {
                    $isCorrect = (int) $choice['is_correct'] === 1;

                    if ($isCorrect) {
                        $totalPoints += $points;
                    }
                }

                quizAttemptAnswerCreateChoice(
                    $attemptId,
                    $questionId,
                    $choiceId,
                    $isCorrect
                );
            }
        } else {
            $answerText = trim($_POST[$fieldName] ?? '');

            quizAttemptAnswerCreateText(
                $attemptId,
                $questionId,
                $answerText
            );
        }
    }

    $score = 0.0;
    if ($maxPoints > 0) {
        $score = round(($totalPoints / $maxPoints) * 100, 2);
    }

    quizAttemptComplete($attemptId, $score);

    require __DIR__ . '/../View/quiz/end_quiz.php';
}
