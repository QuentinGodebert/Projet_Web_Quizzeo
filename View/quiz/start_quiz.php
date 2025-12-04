<?php

declare(strict_types=1);
$pageTitle = 'Début du quiz';
require __DIR__ . '/../layout/header.php';
?>


<?php
$quizTitle = $quiz['title'] ?? ($quiz['name'] ?? 'Quiz');
$quizDescription = $quiz['description'] ?? '';
?>
<main>

    <body>
        <h1><?= htmlspecialchars($quiz['title']) ?></h1>
        <p><?= htmlspecialchars($quiz['description']) ?></p>

        <form action="<?= APP_BASE ?>View/company/question_answer.php" method="POST">
            <input type="hidden" name="quiz_id" value="<?= (int)$quiz['id'] ?>">
            <input type="hidden" name="current" value="0">
            <button type="submit">Commencer le quiz</button>
        </form>
</main>
</body>

</html>

<?php require __DIR__ . '/../layout/footer.php'; ?>