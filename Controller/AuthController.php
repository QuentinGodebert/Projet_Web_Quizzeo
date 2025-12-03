<?php
require_once __DIR__ . '/../Model/UserModel.php';

function loginAction(): void
{
    $email    = '';
    $password = '';
    $errors   = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '') {
            $errors['email'] = 'Email obligatoire.';
        }

        if ($password === '') {
            $errors['password'] = 'Mot de passe obligatoire.';
        }

        if (empty($errors)) {
            $user = userFindByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                $errors['email'] = 'Email ou mot de passe incorrect.';
            } elseif (!$user['is_active']) {
                $errors['email'] = 'Compte désactivé par un administrateur.';
            }
        }

        if (empty($errors)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id'         => $user['id'],
                'role'       => $user['role'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
            ];

            switch ($user['role']) {
                case 'admin':
                    header('Location: ./admin');
                    break;
                case 'school':
                    header('Location: ./school');
                    break;
                case 'company':
                    header('Location: ./company');
                    break;
                default:
                    header('Location: ./user');
            }
            exit;
        }
    }

    $pageTitle = 'Connexion';
    require __DIR__ . '/../View/auth/login.php';
}


function registerAction(): void
{
    $email      = '';
    $password   = '';
    $first_name = '';
    $last_name  = '';
    $role       = '';
    $errors     = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email      = trim($_POST['email']      ?? '');
        $password   = trim($_POST['password']   ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name  = trim($_POST['last_name']  ?? '');
        $role       = trim($_POST['role']       ?? '');

        if ($email === '') {
            $errors['email'] = 'Vous devez spécifier un email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide.';
        }

        if ($password === '') {
            $errors['password'] = 'Vous devez spécifier un mot de passe.';
        } elseif (strlen($password) < 5) {
            $errors['password'] = 'Le mot de passe doit faire au moins 5 caractères.';
        }

        if ($first_name === '') {
            $errors['first_name'] = 'Vous devez spécifier un prénom.';
        }

        if ($last_name === '') {
            $errors['last_name'] = 'Vous devez spécifier un nom.';
        }

        if ($role === '') {
            $errors['role'] = 'Vous devez choisir un rôle.';
        } elseif (!in_array($role, ['user', 'school', 'company'], true)) {
            $errors['role'] = 'Rôle invalide.';
        }
        if (empty($errors)) {
            $existing = userFindByEmail($email);
            if ($existing) {
                $errors['email'] = 'Cet email est déjà utilisé.';
            }
        }
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            userCreate(
                $role,
                $email,
                $hashedPassword,
                $first_name,
                $last_name
            );

            header('Location: ./login');
            exit;
        }
    }

    $pageTitle = 'Création de compte';
    require __DIR__ . '/../View/auth/register.php';
}


function logout(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];
    session_destroy();

    header('Location: ./login');
    exit;
}
