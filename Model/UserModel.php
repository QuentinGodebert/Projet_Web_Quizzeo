<?php

require_once __DIR__ . '/../config/database.php';

/**
 * Trouver un utilisateur par son email.
 *
 * @param string $email L'email de l'utilisateur à rechercher.
 * @return array|null Les données de l'utilisateur ou null s'il n'existe pas.
 */
function findUserByEmail($email)
{
    $pdo = getDatabase();

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    return $user ?: null;
}
