<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/Controller/AdminController.php';
require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/CompanyController.php';
require_once __DIR__ . '/Controller/PublicController.php';
require_once __DIR__ . '/Controller/SchoolController.php';
require_once __DIR__ . '/Controller/QuizController.php';
require_once __DIR__ . '/Controller/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/Projet_Web_Quizzeo';

if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
if ($uri === '' || $uri === false) {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri === '/') {
    require_once __DIR__ . '/View/public/home.php';
} elseif ($uri === '/login' || $uri === '/login.php') {
    loginAction();
} elseif ($uri === '/register' || $uri === '/register.php') {
    registerAction();
} elseif ($uri === '/logout' || $uri === '/logout.php') {
    logout();
} elseif ($uri === '/admin' || $uri === '/admin.php') {
    adminDashboardAction();
} elseif ($uri === '/admin/toggle-user') {
    toggleUserStatusAction();
} elseif ($uri === '/admin/toggle-quiz') {
    toggleQuizStatusAction();
} elseif ($uri === '/company' || $uri === '/company.php') {
    companyDashboardController();
} elseif ($uri === '/school' || $uri === '/school.php') {
    schoolDashboardController();
} elseif ($uri === '/user' || $uri === '/user.php') {
    userDashboardController();
} else {
    http_response_code(404);
    echo 'Page non trouvée : ' . htmlspecialchars($uri);
}
