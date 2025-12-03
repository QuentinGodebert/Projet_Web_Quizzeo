<?php

declare(strict_types=1);

const APP_BASE = '/Projet_Web_Quizzeo';
function getDatabase(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $host     = 'localhost';
        $dbname   = 'quizzeo_db';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    return $pdo;
}
