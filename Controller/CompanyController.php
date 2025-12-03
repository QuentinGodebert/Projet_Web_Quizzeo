<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/csrf.php';

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
    $stmt = $pdo->query('SELECT * FROM companies ORDER BY created_at DESC');
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

    require __DIR__ . '/../View/company/profile.php';
}
function companyCreateController(): void
{
    requireCompanyLogin();

    $errors = [];
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        validate_csrf_or_die();

        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $errors['name'] = 'Le nom de l’entreprise est requis.';
        }

        if ($address === '') {
            $errors['address'] = 'L’adresse est requise.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Veuillez saisir un email valide.';
        }

        if ($description === '') {
            $errors['description'] = 'Veuillez ajouter une description.';
        }

        $pdo = getDatabase();

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id FROM companies WHERE email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->fetch()) {
                $errors['email'] = 'Une entreprise avec cet email existe déjà.';
            }
        }
        if (empty($errors)) {
            $stmt = $pdo->prepare('
                INSERT INTO companies (name, address, email, description, created_at)
                VALUES (:name, :address, :email, :description, NOW())
            ');
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->execute();

            if ($pdo->lastInsertId()) {
                $success = true;
                header('Location: /company/dashboard');
                exit;
            } else {
                $errors['general'] = 'Erreur lors de la création de l’entreprise.';
            }
        }
    }

    require __DIR__ . '/../View/company/create.php';
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
