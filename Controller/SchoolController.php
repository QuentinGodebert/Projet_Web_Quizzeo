<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../Model/QuizModel.php';

function schoolEnsureLoggedIn(): void
{
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'school') {
        header('Location: /Projet_Web_Quizzeo/login');
        exit;
    }
}
function schoolDashboardController(): void
{
    schoolEnsureLoggedIn();

    $schoolId = (int) $_SESSION['user']['id'];
    $quizzes = quizFindByOwner($schoolId);

    require __DIR__ . '/../View/school/dashboard.php';
}
function schoolQuizCreateController(): void
{
    schoolEnsureLoggedIn();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $schoolId = (int) $_SESSION['user']['id'];
        $title = trim($_POST['title'] ?? '');
        $description = $_POST['description'] ?? null;

        if ($title !== '') {
            quizCreate($schoolId, $title, $description);
        }

        header('Location: /Projet_Web_Quizzeo/school');
        exit;
    }

    $quiz = null;
    $questions = [];
    $errors = [];

    require __DIR__ . '/../View/school/quiz_create.php';
}


function schoolQuizEditController(): void
{
    schoolEnsureLoggedIn();

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo 'ID de quiz invalide.';
        return;
    }

    $quiz = quizFindById($id);

    if (!$quiz || (int) $quiz['owner_id'] !== (int) $_SESSION['user']['id']) {
        http_response_code(404);
        echo 'Quiz introuvable ou non autorisé.';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = $_POST['description'] ?? null;

        if ($title !== '') {
            quizUpdate($id, $title, $description);
        }

        header('Location: /Projet_Web_Quizzeo/school');
        exit;
    }

    $questions = [];
    $errors = [];

    require __DIR__ . '/../View/school/quiz_edit.php';
}

function schoolQuizResultController(): void
{
    schoolEnsureLoggedIn();

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        http_response_code(400);
        echo 'ID de quiz invalide.';
        return;
    }

    $quiz = quizFindById($id);

    if (!$quiz || (int) $quiz['owner_id'] !== (int) $_SESSION['user']['id']) {
        http_response_code(404);
        echo 'Quiz introuvable ou non autorisé.';
        return;
    }

    $attempts = [];
    require __DIR__ . '/../View/school/quiz_result.php';
}

