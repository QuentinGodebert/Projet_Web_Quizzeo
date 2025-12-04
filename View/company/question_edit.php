<?php

/** @var array $quiz */
/** @var array $question */
/** @var array $errors */
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="container mt-4">
    <h1>Modifier la question</h1>


    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST"
        action="<?= APP_BASE ?>/company/question_edit?quiz_id=<?= (int)$quiz['id'] ?>&id=<?= (int)$question['id'] ?>">
        <?php if (function_exists('csrf_generate_token')): ?>
            <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars(csrf_generate_token(), ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="intitule" class="form-label">Intitulé de la question</label>
            <textarea
                id="label"
                name="label"
                class="form-control"
                rows="3"
                required><?= htmlspecialchars($question['intitule'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="points" class="form-label">Points</label>
            <input
                type="number"
                id="points"
                name="points"
                class="form-control"
                min="1"
                value="<?= (int)($question['points'] ?? 1) ?>"
                required>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>