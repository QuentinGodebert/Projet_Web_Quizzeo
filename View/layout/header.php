<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user']);
$role = $isLoggedIn ? $_SESSION['user']['role'] : null;

$dashboardUrl = './user';
if ($role === 'admin') {
    $dashboardUrl = './admin';
} elseif ($role === 'school') {
    $dashboardUrl = './school';
} elseif ($role === 'company') {
    $dashboardUrl = './company';
}
?>

<head>
    <link rel="stylesheet" href="./assets/css/style.css">
    <title><?= $pageTitle ?? 'Quizzeo'; ?></title>
    <link rel="icon" type="image/png" href="./assets/images/favicon.png" width="32" height="32">
</head>

<body>
    <header>
        <nav id="nav_bar">
            <ul>
                <li><a href="./">Accueil</a></li>
                <?php if (!$isLoggedIn): ?>
                    <li><a href="<?= $dashboardUrl ?>">Tableau de bord</a></li>
                    <li><a href="./logout">Se déconnecter</a>

                    <?php else: ?>
                    <li><a href="./login">Se connecter</a></li>
                    <li><a href="./register">Créer un compte</a></li>

                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>