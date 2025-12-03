<?php
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Model/QuizModel.php';

function adminDashboardAction(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ./login');
        exit;
    }

    $users = getAllUsers();
    $quizzes = getAllQuizzes();

    $pageTitle = 'Dashboard Admin';
    require __DIR__ . '/../View/admin/dashboard.php';
}

function toggleUserStatusAction(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $userId = (int) $_POST['id'];
        toggleUserStatus($userId);
    }
    header('Location: /Projet_Web_Quizzeo/admin');
    exit;
}

function toggleQuizStatusAction(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $quizId = (int) $_POST['id'];
        toggleQuizStatus($quizId);
    }
    header('Location: /Projet_Web_Quizzeo/admin');
    exit;
}
