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

    <form method="post" action="?route=company_quiz_store">
        <?php if (isset($csrfToken)): ?>
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="title">Titre du quiz</label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-control"
                required
                value="<?= htmlspecialchars($quizData['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="form-group">
            <label for="description">Description (optionnelle)</label>
            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="4"
            ><?= htmlspecialchars($quizData['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-group">
            <label for="status">Statut initial</label>
            <select id="status" name="status" class="form-control">
                <option value="draft"
                    <?= (isset($quizData['status']) && $quizData['status'] === 'draft') ? 'selected' : '' ?>>
                    Brouillon
                </option>
                <option value="launched"
                    <?= (isset($quizData['status']) && $quizData['status'] === 'launched') ? 'selected' : '' ?>>
                    Lancé immédiatement
                </option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer le quiz</button>
            <a href="?route=company_dashboard" class="btn btn-secondary">Retour au tableau de bord</a>
        </div>
    </form>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
