<?php
declare(strict_types=1);

require_once __DIR__ . '/../../helpers/csrf.php';
$csrfToken = csrf_generate_token();
?>

<h1>Modifier un quiz</h1>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $field => $message): ?>
                <li><strong><?= htmlspecialchars($field) ?> :</strong> <?= htmlspecialchars($message) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST"
      action="/Projet_Web_Quizzeo/school/quiz_edit?id=<?= htmlspecialchars((string)$quiz['id']) ?>">
    ...
</form>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <div>
        <label for="title">Titre du quiz :</label>
        <input type="text" name="title" id="title"
               value="<?= htmlspecialchars($_POST['title'] ?? $quiz['title']) ?>" required>
    </div>

    <div>
        <label for="description">Description :</label>
        <textarea name="description" id="description"><?= htmlspecialchars($_POST['description'] ?? $quiz['description']) ?></textarea>
    </div>

    <button type="submit" class="btn">Enregistrer les modifications</button>
</form>

<p><a href="/school/dashboard">← Retour au tableau de bord</a></p>
