<?php

declare(strict_types=1);
$pageTitle = 'Création de compte';
require_once __DIR__ . '/../../View/layout/header.php'; ?>
<main>
    <form method="POST">
        <h1>Connexion</h1>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password">
        </div>

        <button>Se connecter</button>
    </form>
</main>
<?php
require_once __DIR__ . '/../../View/layout/footer.php';
