<?php

declare(strict_types=1);
require __DIR__ . "/../layout/header.php";
?>

<h1>Résultats du quiz</h1>

<?php if (empty($results)): ?>
    <p>Aucun résultat enregistré pour ce quiz.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Participant</th>
                <th>Score</th>
                <th>Terminé le</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td><?= htmlspecialchars($result['first_name'] . ' ' . $result['last_name']) ?></td>
                    <td><?= htmlspecialchars($result['score']) ?>%</td>
                    <td><?= htmlspecialchars($result['finished_at'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="/school/dashboard">← Retour au tableau de bord</a></p>

<?php require __DIR__ . "/../layout/footer.php"; ?>