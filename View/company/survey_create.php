<?php
$pageTitle = 'Créer un quiz';

require __DIR__ . '/../layout/header.php';
?>

<main class="container">
    <header class="page-header">
        <h1>Créer un quiz</h1>
        <p>
            Définissez le titre et la description de votre quiz.
            Les questions seront gérées selon ce qui est prévu dans votre contrôleur / base de données.
        </p>
    </header>

    <?php if (!empty($errors) && is_array($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_BASE ?>/company/survey_create">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(string: csrf_generate_token()) ?>">

        <label for="title">Titre du quiz :</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description"></textarea>

        <button type="submit">Créer</button>
    </form>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>