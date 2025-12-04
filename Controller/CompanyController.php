<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/helper.php';
require_once __DIR__ . '/../Model/QuizModel.php';
require_once __DIR__ . '/../Model/QuestionModel.php';

function requireCompanyLogin(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    if (($_SESSION['user']['role'] ?? '') !== 'company') {
        http_response_code(403);
        echo 'Accès interdit : rôle entreprise requis.';
        exit;
    }
}
function companyDashboardController(): void
{
    requireCompanyLogin();

    $ownerId = (int)($_SESSION['user']['id'] ?? 0);
    $quizs   = quizFindByOwner($ownerId);

    require __DIR__ . '/../View/company/dashboard.php';
}
function companyCreateController(): void
{
    requireCompanyLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo 'Identifiant d’entreprise invalide.';
        return;
    }

    $companyId = (int) $_GET['id'];
    $pdo       = getDatabase();

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
function companyDeleteController(): void
{
    requireCompanyLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo 'Identifiant d’entreprise invalide.';
        return;
    }

    $companyId = (int) $_GET['id'];
    $pdo       = getDatabase();

    $stmt = $pdo->prepare('DELETE FROM companies WHERE id = :id');
    $stmt->bindValue(':id', $companyId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: ' . APP_BASE . '/company');
    exit;
}
function companyQuizEditController(): void
{
    requireCompanyLogin();

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($id <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $errors = [];
    $quiz = quizFindById($id);

    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
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

        $quiz['title'] = $title;
        $quiz['description'] = $description;
    }

    require __DIR__ . '/../View/company/survey_edit.php';
}
function companyQuizResultsController(): void
{
    requireCompanyLogin();
}

function companyQuizLaunchController(): void
{
    requireCompanyLogin();

    $ownerId = (int)$_SESSION['user']['id'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($id <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $pdo = getDatabase();
    $stmt = $pdo->prepare('
        UPDATE quizzes
        SET status = "launched", is_active = 1, updated_at = NOW()
        WHERE id = :id AND owner_id = :owner_id
    ');
    $stmt->execute([
        ':id' => $id,
        ':owner_id' => $ownerId
    ]);

    header('Location: ' . APP_BASE . '/company');
    exit;
}
function companyQuestionsController(): void
{
    requireCompanyLogin();

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

    if ($quizId <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $quiz = quizFindById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company/dashboard');
        exit;
    }

    $questions = getQuestionsByQuizId($quizId);

    require __DIR__ . '/../View/company/questions.php';
}
function companyQuestionCreateController(): void
{
    requireCompanyLogin();

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

    if ($quizId <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $quiz = quizFindById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $errors   = [];
    $intitule = '';
    $points   = 1;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $intitule = trim($_POST['intitule'] ?? '');
        $points   = (int)($_POST['points'] ?? 0);

        if ($intitule === '') {
            $errors[] = "L'intitulé de la question est obligatoire.";
        }
        if ($points <= 0) {
            $errors[] = 'Les points doivent être un entier positif.';
        }

        if (!$errors) {
            $questionId = createQuestion($quizId, $intitule, $points);

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
    requireCompanyLogin();

    $ownerId = (int)$_SESSION['user']['id'];

    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($quizId <= 0 || $id <= 0) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $quiz = quizFindById($quizId, $ownerId);
    if (!$quiz) {
        header('Location: ' . APP_BASE . '/company');
        exit;
    }

    $question = getQuestionById($id, $quizId);
    if (!$question) {
        header('Location: ' . APP_BASE . '/company/questions?quiz_id=' . $quizId);
        exit;
    }

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $intitule = trim($_POST['intitule'] ?? '');
        $points   = (int)($_POST['points'] ?? 0);

        if ($intitule === '') {
            $errors[] = "L'intitulé de la question est obligatoire.";
        }
        if ($points <= 0) {
            $errors[] = 'Les points doivent être un entier positif.';
        }

        if (!$errors) {
            if (updateQuestion($id, $quizId, $intitule, $points)) {
                header('Location: ' . APP_BASE . '/company/questions?quiz_id=' . $quizId);
                exit;
            }
            $errors[] = 'Erreur lors de la mise à jour de la question.';
        }

        $question['intitule'] = $intitule;
        $question['points']   = $points;
    }

    require __DIR__ . '/../View/company/question_edit.php';
}
function companyQuestionDeleteController(): void
{
    requireCompanyLogin();

    $ownerId = (int)$_SESSION['user']['id'];
    $quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
