<?php
declare(strict_types=1);
require_once __DIR__ . '/../../helpers/csrf.php';
?>

<h1>Tableau de bord de l’école</h1>

<a href="/Projet_Web_Quizzeo/school/quiz_create" class="btn">Créer un nouveau quiz</a>


<?php if (empty($quizzes)): ?>
    <p>Aucun quiz créé pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?= htmlspecialchars($quiz['title']) ?></td>
                    <td><?= htmlspecialchars($quiz['description'] ?? '') ?></td>
                    <td><?= htmlspecialchars($quiz['status']) ?></td>
                    <td><?= htmlspecialchars($quiz['created_at'] ?? '') ?></td>
                    <td>
                        <a href="/Projet_Web_Quizzeo/school/quiz_edit?id=<?= urlencode($quiz['id']) ?>">Modifier</a> |
<a href="/Projet_Web_Quizzeo/school/quiz_result?id=<?= urlencode($quiz['id']) ?>">Résultats</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
