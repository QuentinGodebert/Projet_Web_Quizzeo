<?php

declare(strict_types=1);
$pageTitle = 'Fin du quiz';
require __DIR__ . '/../layout/header.php';
?>


<?php
$quizTitle = $quiz['title'] ?? ($quiz['name'] ?? 'Quiz'); ?>

<body>
    <main>
        <h1>Quiz terminé !</h1>
        <p>Votre score : <strong><?= (int)$score ?></strong></p>

        <a href="<?= APP_BASE ?>/">Retour à l’accueil</a>
    </main>
</body>

</html>

<?php require __DIR__ . '/../layout/footer.php'; ?>