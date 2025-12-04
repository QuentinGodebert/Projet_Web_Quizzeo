<?php
$pageTitle = 'Résultats du quiz';

require __DIR__ . '/../layout/header.php';
?>

<main class="container">
    <header class="page-header">
        <h1>Résultats du quiz</h1>

        <?php if (isset($quiz)): ?>
            <h2><?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <?php if (!empty($quiz['description'])): ?>
                <p><?= nl2br(htmlspecialchars($quiz['description'], ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>

            <p>
                Statut :
                <strong><?= htmlspecialchars($quiz['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                Nombre total de réponses :
                <strong><?= (int)($quiz['responses_count'] ?? 0) ?></strong>
            </p>
        <?php endif; ?>

    </header>

    <?php if (!empty($results) && is_array($results)): ?>
        <section class="mt-4">
            <h3>Détail des réponses</h3>

            <table class="table">
                <thead>
                <tr>
                    <th>Question</th>
                    <th>Note moyenne</th>
                    <th>Nombre de réponses</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?= htmlspecialchars($result['question_label'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($result['average_score'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int)($result['responses_count'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php else: ?>
        <p>Aucune réponse pour ce quiz pour le moment.</p>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
