<?php

declare(strict_types=1);
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Controller/AdminController.php';
require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/CompanyController.php';
require_once __DIR__ . '/Controller/PublicController.php';
require_once __DIR__ . '/Controller/SchoolController.php';
require_once __DIR__ . '/Controller/QuizController.php';
require_once __DIR__ . '/Controller/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

if ($basePath !== '' && $basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$uri = rtrim($uri, '/');
if ($uri === '') {
    $uri = '/home';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($uri) {
    case '/home':
        require_once __DIR__ . '/View/public/home.php';
        break;

    case '/login':
        loginAction();
        break;

    case '/register':
        registerAction();
        break;

    case '/admin':
        require_once __DIR__ . '/View/admin/dashboard.php';
        break;

    case '/company':
        require_once __DIR__ . '/View/company/dashboard.php';
        break;

    case '/school':
        require_once __DIR__ . '/View/school/dashboard.php';
        break;

    case '/user':
        require_once __DIR__ . '/View/user/dashboard.php';
        break;

    case '/logout':
        logout();
        break;

    default:
        http_response_code(404);
        echo 'Page non trouvée : ' . htmlspecialchars($uri);
        break;
}
