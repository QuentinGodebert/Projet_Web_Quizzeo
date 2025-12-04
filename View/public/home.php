<?php

/** @var array $quizzes */
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="public-home">
    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenue sur Quizzeo</h1>
            <p>
                Découvrez tous les quiz publiés par les entreprises et écoles.
                Cliquez sur un quiz pour le consulter ou y participer.
            </p>
        </div>
    </section>

    <section class="quiz-section">
        <h2 class="quiz-section-title">Quiz disponibles</h2>

        <?php if (!empty($quizzes) && is_array($quizzes)): ?>
            <div class="quiz-grid">
                <?php foreach ($quizzes as $quiz): ?>
                    <article class="quiz-card">
                        <h3 class="quiz-card-title">
                            <?= htmlspecialchars($quiz['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <?php if (!empty($quiz['description'])): ?>
                            <p class="quiz-card-description">
                                <?= nl2br(htmlspecialchars($quiz['description'], ENT_QUOTES, 'UTF-8')) ?>
                            </p>
                        <?php endif; ?>

                        <div class="quiz-card-meta">
                            <span class="quiz-card-date">
                                Créé le
                                <?= htmlspecialchars($quiz['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>

                        <div class="quiz-card-actions">
                            <?php if (!empty($quiz['access_token'])): ?>
                                <a
                                    class="btn btn-primary"
                                    href="<?= APP_BASE ?>/quiz/start?token=<?= urlencode($quiz['access_token']) ?>">
                                    Voir le quiz
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-primary"
                                    href="<?= APP_BASE ?>/quiz/start?id=<?= (int)($quiz['id'] ?? 0) ?>">
                                    Voir le quiz
                                </a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="quiz-empty-message">
                Aucun quiz publié pour le moment. Revenez un peu plus tard !
            </p>
        <?php endif; ?>
    </section>
</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>