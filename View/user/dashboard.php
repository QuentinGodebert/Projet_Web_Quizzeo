<?php

declare(strict_types=1);
$pageTitle = 'Tableau de bord';
require __DIR__ . '/../layout/header.php';
?>

<main>
<?php
session_start();

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    header('Location: /index.php');
    exit;
}

$currentUserId = $_SESSION['user']['id'];

$quizFile = __DIR__ . '/../../data/quizzes.json';
$responseFile = __DIR__ . '/../../data/responses.json';

$quizzes = [];
if (file_exists($quizFile)) {
    $data = json_decode(file_get_contents($quizFile), true);
    if (is_array($data)) {
        $quizzes = $data;
    }
}

$responses = [];
if (file_exists($responseFile)) {
    $data = json_decode(file_get_contents($responseFile), true);
    if (is_array($data)) {
        $responses = $data;
    }
}

$userResponsesByQuiz = [];

foreach ($responses as $response) {
    if (!isset($response['user_id'], $response['quiz_id'])) {
        continue;
    }
    if ($response['user_id'] == $currentUserId) {
        $quizId = $response['quiz_id'];
        if (!isset($userResponsesByQuiz[$quizId])) {
            $userResponsesByQuiz[$quizId] = [];
        }
        $userResponsesByQuiz[$quizId][] = $response;
    }
}

function getQuizTitle(array $quizzes, $quizId)
{
    foreach ($quizzes as $quiz) {
        if (isset($quiz['id']) && $quiz['id'] == $quizId) {
            return $quiz['title'] ?? 'Quiz sans titre';
        }
    }
    return 'Quiz inconnu';
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard utilisateur</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<h1>Mes questionnaires complétés</h1>

<?php if (empty($userResponsesByQuiz)) : ?>
    <p>Vous n'avez encore répondu à aucun questionnaire.</p>
<?php else : ?>
    <table>
        <thead>
        <tr>
            <th>Questionnaire</th>
            <th>Nombre de tentatives</th>
            <th>Dernier score</th>
            <th>Détail</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($userResponsesByQuiz as $quizId => $attempts) : ?>
            <?php
            usort($attempts, function ($a, $b) {
                $da = $a['submitted_at'] ?? '';
                $db = $b['submitted_at'] ?? '';
                if ($da === $db) {
                    return 0;
                }
                return $da < $db ? 1 : -1;
            });
            $last = $attempts[0];
            $score = $last['score'] ?? null;
            $maxScore = $last['max_score'] ?? null;
            ?>
            <tr>
                <td><?php echo htmlspecialchars(getQuizTitle($quizzes, $quizId)); ?></td>
                <td><?php echo count($attempts); ?></td>
                <td>
                    <?php if ($score !== null && $maxScore !== null) : ?>
                        <?php echo (int) $score; ?> / <?php echo (int) $maxScore; ?>
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <a href="/View/user/attempt_history.php?quiz_id=<?php echo urlencode($quizId); ?>">
                        Voir l'historique
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    <a href="/index.php">Retour à l'accueil</a>
</p>

</body>
</html>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>