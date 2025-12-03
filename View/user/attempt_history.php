<?php

declare(strict_types=1);
$pageTitle = 'Historique des tentatives';
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

$filterQuizId = isset($_GET['quiz_id']) ? $_GET['quiz_id'] : null;

$userAttempts = [];

foreach ($responses as $response) {
    if (!isset($response['user_id'], $response['quiz_id'])) {
        continue;
    }
    if ($response['user_id'] != $currentUserId) {
        continue;
    }
    if ($filterQuizId !== null && $response['quiz_id'] != $filterQuizId) {
        continue;
    }
    $userAttempts[] = $response;
}

usort($userAttempts, function ($a, $b) {
    $da = $a['submitted_at'] ?? '';
    $db = $b['submitted_at'] ?? '';
    if ($da === $db) {
        return 0;
    }
    return $da < $db ? 1 : -1;
});

function getQuizTitle(array $quizzes, $quizId)
{
    foreach ($quizzes as $quiz) {
        if (isset($quiz['id']) && $quiz['id'] == $quizId) {
            return $quiz['title'] ?? 'Quiz sans titre';
        }
    }
    return 'Quiz inconnu';
}

$pageTitle = 'Historique de mes tentatives';
if ($filterQuizId !== null) {
    $pageTitle .= ' - ' . getQuizTitle($quizzes, $filterQuizId);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<h1><?php echo htmlspecialchars($pageTitle); ?></h1>

<?php if (empty($userAttempts)) : ?>
    <p>Aucune tentative trouvée.</p>
<?php else : ?>
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Questionnaire</th>
            <th>Score</th>
            <th>Pourcentage</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($userAttempts as $attempt) : ?>
            <?php
            $quizId = $attempt['quiz_id'];
            $score = $attempt['score'] ?? null;
            $maxScore = $attempt['max_score'] ?? null;
            $percentage = null;
            if ($score !== null && $maxScore) {
                $percentage = round(($score / $maxScore) * 100);
            }
            ?>
            <tr>
                <td><?php echo htmlspecialchars($attempt['submitted_at'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars(getQuizTitle($quizzes, $quizId)); ?></td>
                <td>
                    <?php if ($score !== null && $maxScore !== null) : ?>
                        <?php echo (int) $score; ?> / <?php echo (int) $maxScore; ?>
                    <?php else : ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo $percentage !== null ? $percentage . ' %' : 'N/A'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p>
    <a href="/View/user/dashboard.php">Retour au dashboard</a>
</p>

</body>
</html>

</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>