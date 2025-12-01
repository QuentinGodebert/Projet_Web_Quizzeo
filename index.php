<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/Controller/AdminController.php';
require_once __DIR__ . '/Controller/AuthController.php';
require_once __DIR__ . '/Controller/CompanyController.php';
require_once __DIR__ . '/Controller/PublicController.php';
require_once __DIR__ . '/Controller/SchoolCtronoller.php';
require_once __DIR__ . '/Controller/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath !== '' && $basePath !== '/' && strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
if ($uri === '' || $uri === false) {
    $uri = '/';
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri === '/' || $uri === '/login.php') {
    require_once __DIR__ . '/View/auth/login.php';
} elseif ($uri === '/register' || $uri === '/register.php') {
    require_once __DIR__ . '/View/auth/register.php';
} elseif ($uri === '/admin' || $uri === '/admin.php') {
    require_once __DIR__ . '/View/admin/dashboard.php';
} elseif ($uri === '/company' || $uri === '/company.php') {
    require_once __DIR__ . '/View/company/dashboard.php';
} elseif ($uri === '/school' || $uri === '/school.php') {
    require_once __DIR__ . '/View/school/dashboard.php';
} elseif ($uri === '/user' || $uri === '/user.php') {
    require_once __DIR__ . '/View/user/dashboard.php';
} elseif ($uri === '/logout') {
    require_once __DIR__ . '/Controller/AuthController.php';
    logout();
} else {
    echo 'Page non trouvée';
}
