<?php

declare(strict_types=1);
$pageTitle = 'Début du quiz';
require __DIR__ . '/../layout/header.php';
?>

<main>
<?php
$quizTitle = $quiz['title'] ?? ($quiz['name'] ?? 'Quiz');
$quizDescription = $quiz['description'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($quizTitle); ?> - Quizzeo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($quizTitle); ?></h1>

    <?php if ($quizDescription !== ''): ?>
        <p><?php echo nl2br(htmlspecialchars($quizDescription)); ?></p>
    <?php endif; ?>

    <?php if (empty($questions)): ?>
        <p>Aucune question n’est disponible pour ce quiz.</p>
    <?php else: ?>
        <form method="post" action="/quiz/start">
    <input
        type="hidden"
        name="token"
        value="<?= htmlspecialchars($quiz['access_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >  

            <?php foreach ($questions as $questionIndex => $question): ?>
                <?php
                $questionId = $question['id'];
                $questionLabel = $question['label'] ?? ($question['title'] ?? 'Question');
                $answersList = $question['answers'] ?? [];
                $allowMultiple = !empty($question['allowMultiple']);
                $inputType = $allowMultiple ? 'checkbox' : 'radio';
                $inputName = $allowMultiple
                    ? 'answers[' . $questionId . '][]'
                    : 'answers[' . $questionId . ']';
                ?>
                <div class="question-card">
                    <h2>Question <?php echo $questionIndex + 1; ?></h2>
                    <p><?php echo htmlspecialchars($questionLabel); ?></p>

                    <?php if (!empty($answersList)): ?>
                        <div class="answers-list">
                            <?php foreach ($answersList as $answer): ?>
                                <?php
                                $answerId = $answer['id'];
                                $answerLabel = $answer['label'] ?? ($answer['text'] ?? '');
                                ?>
                                <label class="answer-item">
                                    <input
                                        type="<?php echo $inputType; ?>"
                                        name="<?php echo htmlspecialchars($inputName); ?>"
                                        value="<?php echo htmlspecialchars($answerId); ?>"
                                    >
                                    <span><?php echo htmlspecialchars($answerLabel); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <textarea
                            name="answers[<?php echo htmlspecialchars($questionId); ?>]"
                            rows="3"
                            placeholder="Votre réponse"
                        ></textarea>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit">Valider mes réponses</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>