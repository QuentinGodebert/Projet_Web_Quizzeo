<?php

$pageTitle = 'Tableau de bord entreprise';

require __DIR__ . '/../layout/header.php';
?>

<main class="container">
    <header class="page-header">
        <h1>Tableau de bord entreprise</h1>

        <p>
            Bienvenue
            <?php if (isset($currentUser)): ?>
                <?= htmlspecialchars($currentUser['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                <?= htmlspecialchars($currentUser['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            <?php endif; ?>
        </p>

        <p>
            Depuis ce tableau de bord, vous pouvez créer des quizs, suivre leur état
            et consulter les réponses de vos alternants / stagiaires.
        </p>

        <a href="<?= APP_BASE ?>/company/survey_create" class="btn btn-primary">
            Créer un nouveau quiz
        </a>

    </header>

    <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <section class="mt-4">
        <h2>Mes quiz</h2>

        <?php if (!empty($quizs) && is_array($quizs)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Participants</th>
                        <th>Créé le</th>
                        <th>Dernière mise à jour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizs as $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($quiz['status'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)($quiz['participants_count'] ?? 0) ?></td>
                            <td><?= htmlspecialchars($quiz['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($quiz['updated_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="<?= APP_BASE ?>/company/survey_edit?id=<?= (int)($quiz['id'] ?? 0) ?>">
                                    Modifier
                                </a>
                                |
                                <a href="<?= APP_BASE ?>/company/questions?quiz_id=<?= (int)($quiz['id'] ?? 0) ?>">
                                    Questions
                                </a>
                                <?php if (($quiz['status'] ?? '') === 'draft'): ?>
                                    |
                                    <a href="<?= APP_BASE ?>/company/quiz_launch?id=<?= (int)($quiz['id'] ?? 0) ?>">
                                        Publier
                                    </a>
                                
                                <?php endif; ?>
                                <a href="https://github.com/EvannCarnot/Quizzeo_projet.git" Lien de partage ></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun quiz pour le moment. Créez votre premier quiz pour commencer.</p>
        <?php endif; ?>
    </section>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>