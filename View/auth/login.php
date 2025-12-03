<?php

declare(strict_types=1);
?>

<body>
    <?php require_once __DIR__ . '/../../View/layout/header.php'; ?>

    <main>
        <h1>Connexion</h1>

        <form method="post" action="./login">
            <div>
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email ?? '', ENT_QUOTES) ?>">
                <?php if (!empty($errors['email'])): ?>
                    <p style="color:red;"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    value="<?= htmlspecialchars($password ?? '', ENT_QUOTES) ?>">
                <?php if (!empty($errors['password'])): ?>
                    <p style="color:red;"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Se connecter</button>
        </form>
    </main>
    <footer>
        <?php require_once __DIR__ . '/../../View/layout/footer.php'; ?>
    </footer>
</body>