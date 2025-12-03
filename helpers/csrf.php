<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token_key(): string
{
    return 'csrf_token';
}

function csrf_generate_token(): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION[csrf_token_key()] = $token;
    $_SESSION['csrf_token_time'] = time();
    return $token;
}


function csrf_get_token(): string
{
    if (!isset($_SESSION[csrf_token_key()])) {
        return csrf_generate_token();
    }
    return $_SESSION[csrf_token_key()];
}

function csrf_validate_token(?string $token, int $maxLifetime = 900): bool
{
    if (!isset($_SESSION[csrf_token_key()])) {
        return false;
    }

    if (!is_string($token) || !hash_equals($_SESSION[csrf_token_key()], $token)) {
        return false;
    }

    if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time']) > $maxLifetime) {
        unset($_SESSION[csrf_token_key()], $_SESSION['csrf_token_time']);
        return false;
    }

    return true;
}

function csrf_forget_token(): void
{
    unset($_SESSION[csrf_token_key()], $_SESSION['csrf_token_time']);
}
