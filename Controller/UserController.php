<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Model/QuizAttemptModel.php';

function requireLogin(?string $requiredRole = null): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }

    if ($requiredRole !== null && $_SESSION['user']['role'] !== $requiredRole) {
        http_response_code(403);
        echo 'Accès interdit.';
        exit;
    }
}
function userDashboardController(PDO $pdo): void
{
    requireLogin('user');

    $userId   = (int) $_SESSION['user']['id'];
    $attempts = quizAttemptsByUser($pdo, $userId);

    require __DIR__ . '/../View/user/dashboard.php';
}
function userProfileController(PDO $pdo): void
{
    requireLogin('user');

    $userId = (int) $_SESSION['user']['id'];
    $user   = userFindById($pdo, $userId);

    if (!$user) {
        echo 'Utilisateur introuvable.';
        exit;
    }

    $errors = [];
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validate_csrf_or_die();

        $email      = trim($_POST['email'] ?? '');
        $firstName  = trim($_POST['first_name'] ?? '');
        $lastName   = trim($_POST['last_name'] ?? '');
        $password   = $_POST['password'] ?? '';
        $password2  = $_POST['password_confirm'] ?? '';

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Veuillez saisir un email valide.';
        }

        if ($firstName === '') {
            $errors['first_name'] = 'Le prénom est requis.';
        }

        if ($lastName === '') {
            $errors['last_name'] = 'Le nom est requis.';
        }

        if ($password !== '' || $password2 !== '') {
            if (strlen($password) < 8) {
                $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
            } elseif ($password !== $password2) {
                $errors['password'] = 'Les mots de passe ne correspondent pas.';
            }
        }

        if (empty($errors)) {
            if ($email !== $user['email']) {
                $existing = userFindByEmail($pdo, $email);
                if ($existing && (int) $existing['id'] !== $userId) {
                    $errors['email'] = 'Cet email est déjà utilisé.';
                }
            }
        }

        if (empty($errors)) {
            userUpdateProfile($pdo, $userId, $email, $firstName, $lastName);

            if ($password !== '') {
                userUpdatePassword($pdo, $userId, $password);
            }

            $updated = userFindById($pdo, $userId);
            $_SESSION['user']['first_name'] = $updated['first_name'];
            $_SESSION['user']['last_name']  = $updated['last_name'];

            $user    = $updated;
            $success = true;
        }
    }

    require __DIR__ . '/../View/user/profile.php';
}
