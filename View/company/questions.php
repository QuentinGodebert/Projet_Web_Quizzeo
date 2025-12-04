<?php

/** @var array $quiz */
/** @var array $questions */
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="container mt-4">
    <h1>Questions du quiz : <?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>

    <p class="mb-3">
        <a class="btn btn-primary"
            href="<?= APP_BASE ?>/company/question_create?quiz_id=<?= (int)$quiz['id'] ?>">
            Ajouter une question
        </a>
    </p>

    <?php if (!empty($questions)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Intitulé</th>
                    <th>Points</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($questions as $q): ?>
                    <tr>
                        <td><?= (int)$q['id'] ?></td>
                        <td><?= htmlspecialchars($q['intitule'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int)($q['points'] ?? 0) ?></td>
                        <td>
                            <a href="<?= APP_BASE ?>/company/question_edit?quiz_id=<?= (int)$quiz['id'] ?>&id=<?= (int)$q['id'] ?>">
                                Modifier
                            </a>

                            |
                            <a href="<?= APP_BASE ?>/company/question_delete?quiz_id=<?= (int)$quiz['id'] ?>&id=<?= (int)$q['id'] ?>"
                                onclick="return confirm('Supprimer cette question ?');">
                                Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune question pour ce quiz pour le moment.</p>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>