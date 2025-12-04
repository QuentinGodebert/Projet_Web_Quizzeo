<?php

declare(strict_types=1);
/** @var array $quiz */ ?>
<?php /** @var array $errors */ ?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="container mt-4">
    <h1>Modifier le quiz</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_BASE ?>/company/survey_edit?id=<?= (int)$quiz['id'] ?>">
        <?php if (function_exists('csrf_generate_token')): ?>
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars(csrf_generate_token(), ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="title">Titre du quiz</label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-control"
                value="<?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required>
        </div>

        <div class="mb-3">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="4"><?= htmlspecialchars($quiz['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= APP_BASE ?>/company" class="btn btn-secondary">Annuler</a>
    </form>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>