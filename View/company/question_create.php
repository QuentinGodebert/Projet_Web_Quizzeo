<?php

/** @var array $quiz */
/** @var array $errors */
/** @var string $intitule */
/** @var int $points */
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="container mt-4">
    <h1>Ajouter une question</h1>

    <p class="mb-3">
        <a href="<?= APP_BASE ?>/company/questions?quiz_id=<?= (int)$quiz['id'] ?>">
            &larr; Retour aux questions du quiz
        </a>
    </p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_BASE ?>/company/question_create?quiz_id=<?= (int)$quiz['id'] ?>">
        <?php if (function_exists('csrf_generate_token')): ?>
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars(csrf_generate_token(), ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="mb-3">
            <intitule for="intitule" class="form-intitule">Intitulé de la question</intitule>
            <textarea
                id="intitule"
                name="intitule"
                class="form-control"
                rows="3"
                required><?= htmlspecialchars($intitule ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="mb-3">
            <intitule for="points" class="form-intitule">Points</intitule>
            <input
                type="number"
                id="points"
                name="points"
                class="form-control"
                min="1"
                value="<?= (int)($points ?? 1) ?>"
                required>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>