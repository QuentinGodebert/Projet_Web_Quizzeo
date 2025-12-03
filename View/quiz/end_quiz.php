<?php

declare(strict_types=1);
$pageTitle = 'Fin du quiz';
require __DIR__ . '/../layout/header.php';
?>

<main>
<?php
$quizTitle = $quiz['title'] ?? ($quiz['name'] ?? 'Quiz');
$scorePercent = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats - <?php echo htmlspecialchars($quizTitle); ?> - Quizzeo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h1>Résultats du quiz</h1>

    <h2><?php echo htmlspecialchars($quizTitle); ?></h2>

    <p>
        Votre score est de
        <strong><?php echo htmlspecialchars((string)$score); ?> / <?php echo htmlspecialchars((string)$maxScore); ?></strong>
        (<?php echo htmlspecialchars((string)$scorePercent); ?> %)
    </p>

    <a href="index.php">Retour à l’accueil</a>
</div>
</body>
</html>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>