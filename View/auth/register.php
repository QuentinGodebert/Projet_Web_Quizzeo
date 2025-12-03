<?php

declare(strict_types=1);
require_once __DIR__ . '/../../Controller/AuthController.php';

$pageTitle = 'Création de compte';
require_once __DIR__ . '/../../View/layout/header.php'; ?>
<main>
    <form method="POST">
        <h1>Connexion</h1>

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>"
                <?php if (!empty($errors['email'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errors['email']) ?></p>
            <?php endif; ?>>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" value="<?= htmlspecialchars($password) ?>"
                <?php if (!empty($errors['password'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errors['password']) ?></p>
            <?php endif; ?>>
        </div>

        <div>
            <label for="first_name">Prénom</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>"
                <?php if (!empty($errors['first_name'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errors['first_name']) ?></p>
            <?php endif; ?>>
        </div>
        <div>
            <label for="last_name">Nom</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>"
                <?php if (!empty($errors['last_name'])): ?>
                <p style="color:red;"><?= htmlspecialchars($errors['last_name']) ?></p>
            <?php endif; ?>>
        </div>
        <div>
            <label for="role">Rôle</label>
            <select id="role" name="role">
                <option value="" disabled selected hidden>Sélectionner un rôle</option>
                <option value="user">Utilisateur</option>
                <option value="school">École</option>
                <option value="company">Entreprise</option>
            </select>
        </div>
        <button>Créer mon compte</button>
    </form>
</main>
<?php
require_once __DIR__ . '/../../View/layout/footer.php';
