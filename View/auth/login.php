<?php

declare(strict_types=1);

require_once __DIR__ . '/../../controllers/auth_controller.php';

$email = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = handleLogin($_POST);

    // Si la connexion a réussi, rediriger vers la page d'accueil
    // Sinon, afficher les erreurs
}

?>

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