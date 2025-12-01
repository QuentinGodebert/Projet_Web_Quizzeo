<?php

require_once __DIR__ . '/../Model/UserModel.php';

/**
 * Gérer le processus de connexion.
 *
 * @param array $postData Les données POST du formulaire de connexion.
 * @return array Un tableau contenant le statut de la connexion et les erreurs éventuelles.
 */
function handleLogin(array $postData): array
{
    try {
        $errors = [];
        $email = trim($postData['email'] ?? '');
        $password = $postData['password'] ?? '';

        //Vérification des champs obligatoires et validation
        // ...

        $user = findUserByEmail($email);

        // Vérification du mot de passe
        // ...

        // Authentification réussie
        // Faire quelque chose, par exemple démarrer une session
        // ET retourner le succès
        return ['success' => true];
    } catch (Exception $e) {
        // Retourner une erreur générique
        return ['success' => false, 'errors' => ['Une erreur est survenue lors de la connexion.']];
    }
}
