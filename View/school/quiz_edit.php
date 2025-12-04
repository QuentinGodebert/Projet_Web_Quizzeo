<?php

declare(strict_types=1);
require_once __DIR__ . '/../../helpers/csrf.php';
$csrfToken = csrf_generate_token();
require __DIR__ . '/../layout/header.php';
?>

<h1>Modifier le quiz</h1>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $field => $message): ?>
                <li><strong><?= htmlspecialchars($field) ?> :</strong> <?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- AUCUN action : le POST repart sur la même URL, ex: /Projet_Web_Quizzeo/school/quiz_edit?id=2 -->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div>
        <label for="title">Titre du quiz :</label>
        <input type="text" name="title" id="title"
               value="<?= htmlspecialchars($quiz['title'] ?? '') ?>" required>
    </div>

    <div>
        <label for="description">Description :</label>
        <textarea name="description" id="description"><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn">Enregistrer</button>
</form>

<p><a href="/Projet_Web_Quizzeo/school">← Retour au tableau de bord</a></p>
