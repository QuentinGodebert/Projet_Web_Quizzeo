<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Model/QuizModel.php';
require_once __DIR__ . '/../helpers/helper.php';
require_once __DIR__ . '/../Model/QuizModel.php';
require_once __DIR__ . '/../Model/QuestionModel.php';

$pdo = getDatabase();
function requireCompanyLogin(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'company') {
        http_response_code(403);
        echo 'Accès interdit : rôle entreprise requis.';
        exit;
    }
}
function companyDashboardController(): void
{
    require_once __DIR__ . '/../Model/QuizModel.php';
    $ownerId = (int)($_SESSION['user']['id'] ?? 0);

    $quizs = quizFindByOwner($ownerId);

    require __DIR__ . '/../View/company/dashboard.php';
}
function companyProfileController(): void
{
    requireCompanyLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo 'Identifiant d’entreprise invalide.';
        return;
    }

    $companyId = (int) $_GET['id'];
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM companies WHERE id = :id');
    $stmt->bindValue(':id', $companyId, PDO::PARAM_INT);
    $stmt->execute();
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        http_response_code(404);
        echo 'Entreprise introuvable.';
        return;
    }
}

function companyCreateController(): void
{
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $errors = [];
    $title = '';
    $description = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $errors[] = 'Le titre est obligatoire.';
        }

        if (!$errors) {
            $ownerId = (int) $_SESSION['user']['id'];
            if (quizCreate($ownerId, $title, $description)) {
                header('Location: ' . APP_BASE . '/company');
                exit;
            }
            $errors[] = "Erreur lors de la création du quiz.";
        }
    }

    require __DIR__ . '/../View/company/survey_create.php';
}



function companyDeleteController(): void
{
    requireCompanyLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo 'Identifiant d’entreprise invalide.';
        return;
    }

    $companyId = (int) $_GET['id'];
    $pdo = getDatabase();

    $stmt = $pdo->prepare('DELETE FROM companies WHERE id = :id');
    $stmt->bindValue(':id', $companyId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: /company/dashboard');
    exit;
}
require_once __DIR__ . '/../Model/QuizModel.php';

function companyQuizEditController(): void
{
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $ownerId = (int) $_SESSION['user']['id'];
    $id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $errors = [];
    $quiz   = quizFindById($id);

    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $errors[] = 'Le titre est obligatoire.';
        }

        if (!$errors) {
            if (quizUpdate($id, $title, $description)) {
                header('Location: ' . APP_BASE . '/company');
                exit;
            }

            $errors[] = "Erreur lors de la mise à jour du quiz.";
        }

        $quiz['title']       = $title;
        $quiz['description'] = $description;
    }

    require __DIR__ . '/../View/company/survey_edit.php';
}

function companyQuizResultsController(): void {}

require_once __DIR__ . '/../Model/QuizModel.php';

function companyQuizLaunchController(): void
{
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $ownerId = (int)$_SESSION['user']['id'];
    $id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    publishQuiz($id, $ownerId);

    header('Location: ' . APP_BASE . '/company');
    exit;
}
function companyQuestionsController(): void
{
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId  = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

    if ($quizId <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $quiz = getQuizById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $questions = getQuestionsByQuizId($quizId);

    require __DIR__ . '/../View/company/questions.php';
}

function companyQuestionCreateController(): void
{
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId  = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

    if ($quizId <= 0) {
        header('Location: ' . APP_BASE . '/company/dashboard');
        exit;
    }

    $quiz = quizFindById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company/dashboard');
        exit;
    }

    $errors = [];
    $label  = '';
    $points = 1;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $label  = trim($_POST['label'] ?? '');
        $points = (int)($_POST['points'] ?? 0);

        if ($label === '') {
            $errors[] = 'L’intitulé de la question est obligatoire.';
        }
        if ($points <= 0) {
            $errors[] = 'Les points doivent être un entier positif.';
        }

        if (!$errors) {
            $questionId = createQuestion($quizId, $label, $points);

            if ($questionId !== null) {
                header('Location: ' . APP_BASE . '/company/questions?quiz_id=' . $quizId);
                exit;
            }

            $errors[] = 'Erreur lors de la création de la question.';
        }
    }

    require __DIR__ . '/../View/company/question_create.php';
}

function companyQuestionEditController(): void
{
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId  = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($quizId <= 0 || $id <= 0) {
        header('Location: ' . APP_BASE . '/company/dashboard');
        exit;
    }

    $quiz = quizFindById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company/dashboard');
        exit;
    }

    $question = getQuestionById($id, $quizId);
    if (!$question) {
        header('Location: ' . APP_BASE . '/company/questions?quiz_id=' . $quizId);
        exit;
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $label  = trim($_POST['label'] ?? '');
        $points = (int)($_POST['points'] ?? 0);

        if ($label === '') {
            $errors[] = 'L’intitulé de la question est obligatoire.';
        }
        if ($points <= 0) {
            $errors[] = 'Les points doivent être un entier positif.';
        }

        if (!$errors) {
            if (updateQuestion($id, $quizId, $label, $points)) {
                header('Location: ' . APP_BASE . '/company/questions?quiz_id=' . $quizId);
                exit;
            }

            $errors[] = 'Erreur lors de la mise à jour de la question.';
        }

        $question['label']  = $label;
        $question['points'] = $points;
    }

    require __DIR__ . '/../View/company/question_edit.php';
}

function companyQuestionDeleteController(): void
{
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'company') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId  = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($quizId <= 0 || $id <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $quiz = quizFindById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    deleteQuestion($id, $quizId);

    header('Location: ' . APP_BASE . '/company/questions?quiz_id=' . $quizId);
    exit;
}
