<?php

/** @var array $quiz */
/** @var array $questions */
?>

<?php include __DIR__ . '/../layout/header.php'; ?>

<main class="container mt-4">
    <h2>Question <?= $currentIndex + 1 ?></h2>
    <p><?= htmlspecialchars($question['intitule']) ?></p>

    <form action="<?= APP_BASE ?>/quiz/end_question" method="POST">
        <input type="hidden" name="quiz_id" value="<?= (int)$quiz['id'] ?>">
        <input type="hidden" name="current" value="<?= $currentIndex + 1 ?>">

        <label>Votre réponse :</label>
        <input type="text" name="answers[<?= $currentIndex ?>]" required>

        <button type="submit">
            <?= $currentIndex + 1 < count($questions) ? 'Suivant' : 'Terminer' ?>
        </button>
    </form>

</main>

<?php include __DIR__ . '/../layout/footer.php'; ?>