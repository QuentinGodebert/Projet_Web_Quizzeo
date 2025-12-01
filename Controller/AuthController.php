<?php

$_SESSION['user'] = [
    'id'         => $user['id'],
    'role'       => $user['role'],
    'first_name' => $user['first_name'],
    'last_name'  => $user['last_name'],
];

function logout(): void
{
    session_start();
    $_SESSION = [];
    session_destroy();
    header('Location: /');
    exit;
}
