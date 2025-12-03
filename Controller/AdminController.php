<?php
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Model/QuizModel.php';

// dans adminRequireAuth(), par exemple
function adminRequireAuth(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // On ne redirige PAS si on est déjà sur /login ou /register
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    if (empty($_SESSION['user']) && $currentPath !== APP_BASE . '/login' && $currentPath !== APP_BASE . '/register') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }

    if (!empty($_SESSION['user']) && $_SESSION['user']['role'] !== 'admin') {
        header('Location: ' . APP_BASE . '/login');
        exit;
    }
}


function adminDashboardAction(): void
{
    adminRequireAuth();

    $users   = getAllUsers();
    $quizzes = getAllQuizzes();

    $pageTitle = 'Dashboard Admin';
    require __DIR__ . '/../View/admin/dashboard.php';
}

function toggleUserStatusAction(): void
{
    adminRequireAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $userId = (int) $_POST['id'];
        toggleUserStatus($userId);
    }

    // PAS DE header('Location: ...') ICI
    // On réaffiche juste le dashboard
    adminDashboardAction();
}

function toggleQuizStatusAction(): void
{
    adminRequireAuth();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $quizId = (int) $_POST['id'];
        toggleQuizStatus($quizId);
    }

    // PAS DE header('Location: ...') ICI
    adminDashboardAction();
}
