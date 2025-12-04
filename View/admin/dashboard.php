<?php

declare(strict_types=1);

$pageTitle = 'Tableau de bord';
require __DIR__ . '/../layout/header.php';
?>
<html>

<body>
    <main>

        <section class="admin-dashboard">
            <h1>Dashboard Administrateur</h1>
            <h2>Utilisateurs</h2>
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= $user['is_active'] ? '✅ Actif' : '❌ Inactif' ?></td>
                            <td>
                                <form method="post" action="./admin/toggle-user">
                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                    <button type="submit">
                                        <?= $user['is_active'] ? 'Désactiver' : 'Activer' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="admin-dashboard">
            <h2>Quiz</h2>
            <table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Statut</th>
                        <th>Actif</th>
                        <th>Créé le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= $quiz['id'] ?></td>
                            <td><?= htmlspecialchars($quiz['title']) ?></td>
                            <td><?= htmlspecialchars($quiz['status']) ?></td>
                            <td><?= $quiz['is_active'] ? '✅ Oui' : '❌ Non' ?></td>
                            <td><?= htmlspecialchars($quiz['created_at']) ?></td>
                            <td>
                                <form method="post" action="./admin/toggle-quiz">
                                    <input type="hidden" name="id" value="<?= $quiz['id'] ?>">
                                    <button type="submit">
                                        <?= $quiz['is_active'] ? 'Désactiver' : 'Activer' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <?php require __DIR__ . '/../layout/footer.php'; ?>
    </footer>
</body>

</html>