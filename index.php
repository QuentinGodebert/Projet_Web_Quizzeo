<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Controller/AdminController.php';
require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/CompanyController.php';
require_once __DIR__ . '/Controller/PublicController.php';
require_once __DIR__ . '/Controller/QuizController.php';
require_once __DIR__ . '/Controller/SchoolController.php';
require_once __DIR__ . '/Controller/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ANCIENNE MÉTHODE QUE TU AVAIS (elle est bien)
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); // ex : /Projet_Web_Quizzeo

if ($basePath !== '' && $basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));               // ex : /login
}

if ($uri === '' || $uri === false) {
    $uri = '/';
}

$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* ----------- ROUTES ----------- */

if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/View/public/home.php';
}

/* Auth */ elseif ($uri === '/login' || $uri === '/login.php') {
    loginAction();
} elseif ($uri === '/register' || $uri === '/register.php') {
    registerAction();
} elseif ($uri === '/logout' || $uri === '/logout.php') {
    logout();
}

/* Admin */ elseif ($uri === '/admin' || $uri === '/admin.php') {
    adminDashboardAction();
} elseif ($uri === '/admin/toggle-user' && $method === 'POST') {
    toggleUserStatusAction();
} elseif ($uri === '/admin/toggle-quiz' && $method === 'POST') {
    toggleQuizStatusAction();
}

/* Company */ elseif ($uri === '/company' || $uri === '/company.php') {
    companyDashboardAction();
}

/* School */ elseif ($uri === '/school' || $uri === '/school.php') {
    schoolDashboardAction();
}

/* User */ elseif ($uri === '/user' || $uri === '/user.php') {
    userDashboardAction();
}

/* 404 */ else {
    http_response_code(404);
    echo 'Page non trouvée : ' . htmlspecialchars($uri);
}
