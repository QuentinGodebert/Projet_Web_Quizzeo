<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../Model/QuizModel.php';

function requireCompanyLogin(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'company') {
        http_response_code(403);
        echo 'Accès interdit : rôle entreprise requis.';
        exit;
    }
}
function companyDashboardController(): void
{
    requireCompanyLogin();

    $pdo = getDatabase();
    $stmt = $pdo->query('SELECT * FROM users ORDER BY created_at DESC');
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    require __DIR__ . '/../View/company/dashboard.php';
}
function companyProfileController(): void
{
    requireCompanyLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo 'Identifiant d’entreprise invalide.';
        return;
    }

    $companyId = (int) $_GET['id'];
    $pdo = getDatabase();

    $stmt = $pdo->prepare('SELECT * FROM companies WHERE id = :id');
    $stmt->bindValue(':id', $companyId, PDO::PARAM_INT);
    $stmt->execute();
    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$company) {
        http_response_code(404);
        echo 'Entreprise introuvable.';
        return;
    }
}
function companyCreateController(): void
{
    $pdo = getDatabase();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validate_csrf_or_die();

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($title === '') {
            $error = "Le titre est obligatoire.";
        } else {
            $owner_id = $_SESSION['user']['id'] ?? null;

            if ($owner_id && createQuiz($pdo, $title, $description, $owner_id)) {
                header('Location: ./company');
                exit;
            } else {
                $error = "Erreur lors de la création du quiz.";
            }
        }
    }

    require __DIR__ . '/../View/company/survey_create.php';
}

function companyDeleteController(): void
{
    requireCompanyLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        http_response_code(400);
        echo 'Identifiant d’entreprise invalide.';
        return;
    }

    $companyId = (int) $_GET['id'];
    $pdo = getDatabase();

    $stmt = $pdo->prepare('DELETE FROM companies WHERE id = :id');
    $stmt->bindValue(':id', $companyId, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: /company/dashboard');
    exit;
}
