<?php

declare(strict_types=1);

$pageTitle = 'Tableau de bord utilisateur';
require __DIR__ . '/../layout/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    header('Location: /login');
    exit;
}

$currentUserId = (int)$_SESSION['user']['id'];
$pdo = getDatabase();

$stmt = $pdo->prepare('
    SELECT 
        q.id AS quiz_id,
        q.title AS quiz_title,
        COUNT(a.id) AS total_attempts,
        MAX(a.finished_at) AS last_attempt_date,
        (
            SELECT qa.score
            FROM quiz_attempts qa
            WHERE qa.quiz_id = q.id AND qa.user_id = :uid
            ORDER BY qa.finished_at DESC
            LIMIT 1
        ) AS last_score
    FROM quizzes q
    INNER JOIN quiz_attempts a ON a.quiz_id = q.id
    WHERE a.user_id = :uid
    GROUP BY q.id, q.title
    ORDER BY last_attempt_date DESC
');
$stmt->execute([':uid' => $currentUserId]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="container">
    <h1>Mes quiz complétés</h1>

    <?php if (empty($results)): ?>
        <p>Vous n'avez encore répondu à aucun quiz.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Quiz</th>
                    <th>Nombre de tentatives</th>
                    <th>Dernier score</th>
                    <th>Historique</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['quiz_title'] ?? 'Quiz inconnu', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= (int)$row['total_attempts']; ?></td>
                        <td>
                            <?= $row['last_score'] !== null
                                ? htmlspecialchars((string)$row['last_score'], ENT_QUOTES, 'UTF-8')
                                : 'N/A'; ?>
                        </td>
                        <td>
                            <a href="/user/attempt_history?quiz_id=<?= urlencode((string)$row['quiz_id']); ?>">
                                Voir l’historique
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>