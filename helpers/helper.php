<?php

require_once __DIR__ . '/csrf.php';

function view(string $template, array $data = []): void
{
    extract($data);
    require __DIR__ . '/../views/' . $template . '.php';
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function csrf_input(): string
{
    $token = csrf_get_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function validate_csrf_or_die(): void
{
    $token = $_POST['csrf_token'] ?? null;
    if (!csrf_validate_token($token)) {
        http_response_code(419);
        echo 'Token CSRF invalide';
        exit;
    }
}
