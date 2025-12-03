<?php 
declare(strict_types=1);

require_once __DIR__ ."/../config/database.php";
require_once __DIR__ ."/../helpers/csrf.php";

require_once __DIR__ ."/../Model/QuizModel.php";
require_once __DIR__ ."/../Model/QuestionModel.php";
require_once __DIR__ ."/../Model/AnswerModel.php";
require_once __DIR__ ."/../Model/QuizAttemptModel.php";
require_once __DIR__ ."/../Model/QuizAttemptAnswerModel.php";

function quiz_start_controller(PDO $pdo): void {
    if(!isset($_GET['token'])) {
        http_response_code(400);
        echo "token de quiz manquant.";
        return;
}
$token = $_GET['token'];

$quiz = quizFindByAccessToken($pdo, $token);

if (
    !$quiz ||
    !$quiz['is_active'] ||
    $quiz['status'] !== "launched"
) {
    http_response_code(404);
    echo "quiz introuvable.";
    return;
}

$questions = questionFindByQuiz($pdo, (int)$quiz['id']);

$choicByQuestion = [];
foreach($questions as $question) {
    if ($question['type'] === "qcm") {
        $choicByQuestion[$question['id']] = 
        choiceFindByQuestion($pdo, (int)$question['id']);
    }
}
require __DIR__ . "/../View/quiz/start_quiz.php";
}

function quiz_submit_controller(PDO $pdo): void
{
    if($_SERVER['REQUEST_METHOD'] !== "POST") {
        http_response_code(405);
        echo "Méthode non autorisée.";
        return;
    }

    validate_csrf_or_die();

    if(!isset($_POST['token'])) {
        http_response_code(400);
        echo "token de quiz manquant.";
        return;
    }

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "user") {
        http_response_code(403);
        echo "Vous devez être connecté avec un compte utilisateur pour répondre.";
        return;
    }

    $token = $_GET['token'];
    $quiz = quizFindByAccessToken($pdo, $token);

    if (
        !$quiz ||
        !$quiz['is_active'] ||
        $quiz['status'] !== "launched"
        ) {
            http_response_code(400);
            echo "Quiz introuvable ou non disponible";
            return;
        }

        $userId = (int)$_SESSION['user']['id'];
        $quizId = (int)$quiz['id'];
        $questions = questionFindByQuiz($pdo, $quizId);

        if (empty($questions)) {
            http_response_code(400);
            echo "Aucune question trouvée pour ce quiz.";
            return;
        }

        $attemptId = quizAttemptStart($pdo, $quizId, $userId);

        $totalPoints = 0;
        $maxPoints = 0;

        foreach ($questions as $question) {
            $questionId = (int)$question['id'];
            $type = $question['type'];
            $points = (int)($question['points'] ?? 0);
            $maxPoints += max(0,$points);

            $fieldName = "question_" . $questionId;
            if ($type == "qcm") {
                $choiceId = isset($_POST[$fieldName]) ? (int)$_POST[$fieldName] : 0;
                $is_correct = null;
                if ($choiceId > 0) {
                    $choice = choiceFindById($pdo, $choiceId);
                    if ($choice && (int)$choice['question_id'] === $questionId) {
                        $is_correct = $choice && (int) $choice['is_correct'] === 1;

                        if ($is_correct) {
                            $totalPoints += $points;
                        }
                    }

                    quizAttemptAnswerCreateChoice(
                $pdo,
                $attemptId,
                $questionId,
                $choiceId > 0 ? $choiceId : null,
                $is_correct
            );
        } else {
            $answerText = trim($_POST[$fieldName] ?? '');

            quizAttemptAnswerCreateText(
                $pdo,
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

    quizAttemptComplete($pdo, $attemptId, $score);
    require __DIR__ . "/../View/quiz/end_quiz.php";
    
            }}