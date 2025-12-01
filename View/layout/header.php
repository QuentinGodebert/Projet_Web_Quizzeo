<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user']);
$role = $isLoggedIn ? $_SESSION['user']['role'] : null;

$dashboardUrl = '/user';
if ($role === 'admin') {
    $dashboardUrl = '/admin';
} elseif ($role === 'school') {
    $dashboardUrl = '/school';
} elseif ($role === 'company') {
    $dashboardUrl = '/company';
}
?>

<body>
    <header>
        <nav>
            <ul>
                <?php if (!$isLoggedIn): ?>
                    <li><a href="/">Se connecter</a></li>
                    <li><a href="/register">Créer un compte</a></li>
                <?php else: ?>
                    <li><a href="<?= $dashboardUrl ?>">Tableau de bord</a></li>
                    <li><a href="/logout">Se déconnecter</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>