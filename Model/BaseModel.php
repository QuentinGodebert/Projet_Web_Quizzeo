<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
function dbFindOne(string $sql, array $params = []): ?array
{
    $pdo = getDatabase();

    try {
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    } catch (PDOException $e) {
        error_log('dbFindOne error: ' . $e->getMessage());
        return null;
    }
}
function dbFindAll(string $sql, array $params = []): array
{
    $pdo = getDatabase();

    try {
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (PDOException $e) {
        error_log('dbFindAll error: ' . $e->getMessage());
        return [];
    }
}
function dbExecute(string $sql, array $params = []): int
{
    $pdo = getDatabase();

    try {
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log('dbExecute error: ' . $e->getMessage());
        return 0;
    }
}
