<?php

declare(strict_types=1);

$pageTitle = 'Historique des tentatives';
require __DIR__ . '/../layout/header.php';

require_once __DIR__ . '/../../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    header('Location: /login');
    exit;
}

$currentUserId = (int)$_SESSION['user']['id'];
$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

if ($quizId <= 0) {
    echo '<main class="container"><p>Quiz invalide.</p></main>';
    require __DIR__ . '/../layout/footer.php';
    exit;
}

$pdo = getDatabase();

$stmtQuiz = $pdo->prepare('SELECT title FROM quizzes WHERE id = :id');
$stmtQuiz->execute([':id' => $quizId]);
$quiz = $stmtQuiz->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    echo '<main class="container"><p>Quiz introuvable.</p></main>';
    require __DIR__ . '/../layout/footer.php';
    exit;
}

$quizTitle = $quiz['title'] ?? 'Quiz';

$stmt = $pdo->prepare('
    SELECT id, started_at, finished_at, score
    FROM quiz_attempts
    WHERE user_id = :uid AND quiz_id = :qid
    ORDER BY finished_at DESC
');
$stmt->execute([':uid' => $currentUserId, ':qid' => $quizId]);
$attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="container">
    <h1>Historique du quiz : <?= htmlspecialchars($quizTitle, ENT_QUOTES, 'UTF-8'); ?></h1>

    <?php if (empty($attempts)): ?>
        <p>Aucune tentative enregistrée pour ce quiz.</p>
    <?php else: ?>
        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Durée (s)</th>
                <th>Score</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($attempts as $attempt): ?>
                <?php
                $start = $attempt['started_at'] ?? null;
                $end = $attempt['finished_at'] ?? null;
                $duration = null;

                if ($start && $end) {
                    $duration = strtotime($end) - strtotime($start);
                }
                ?>
                <tr>
                    <td><?= htmlspecialchars($attempt['finished_at'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= $duration !== null ? $duration : '—'; ?></td>
                    <td><?= htmlspecialchars((string)$attempt['score'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="/user/dashboard">Retour au tableau de bord</a></p>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>
