<?php

class AdminController
{
    private $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require __DIR__ . '/../config/database.php';
        $this->pdo = $pdo;

        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: index.php');
            exit;
        }
    }

    public function dashboard()
    {
        $users = $this->getAllUsers();
        $quizzes = $this->getAllQuizzesWithStats();
        $totalUsers = count($users);
        $totalQuizzes = count($quizzes);
        $totalAttempts = $this->getTotalAttempts();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function toggleUser()
    {
        if (!isset($_POST['user_id'])) {
            header('Location: index.php?controller=admin&action=dashboard');
            exit;
        }

        $id = (int) $_POST['user_id'];

        $sql = "UPDATE users 
                SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        header('Location: index.php?controller=admin&action=dashboard');
        exit;
    }

    public function toggleQuiz()
    {
        if (!isset($_POST['quiz_id'])) {
            header('Location: index.php?controller=admin&action=dashboard');
            exit;
        }

        $id = (int) $_POST['quiz_id'];

        $sql = "UPDATE quizzes 
                SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        header('Location: index.php?controller=admin&action=dashboard');
        exit;
    }

    private function getAllUsers()
    {
        $sql = "SELECT id, role, email, first_name, last_name, is_active, created_at, updated_at
                FROM users
                ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAllQuizzesWithStats()
    {
        $sqlQuizzes = "SELECT q.id, q.owner_id, q.title, q.description, q.status, q.is_active,
                              q.created_at, q.updated_at,
                              u.first_name, u.last_name, u.email
                       FROM quizzes q
                       INNER JOIN users u ON u.id = q.owner_id
                       ORDER BY q.created_at DESC";
        $stmt = $this->pdo->query($sqlQuizzes);
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sqlStats = "SELECT quiz_id, COUNT(*) AS attempts_count, AVG(score) AS avg_score
                     FROM quiz_attempts
                     GROUP BY quiz_id";
        $stmtStats = $this->pdo->query($sqlStats);
        $statsRows = $stmtStats->fetchAll(PDO::FETCH_ASSOC);

        $stats = [];
        foreach ($statsRows as $row) {
            $stats[(int) $row['quiz_id']] = [
                'attempts_count' => (int) $row['attempts_count'],
                'avg_score' => $row['avg_score'] !== null ? (float) $row['avg_score'] : null
            ];
        }

        foreach ($quizzes as &$quiz) {
            $id = (int) $quiz['id'];
            $quiz['attempts_count'] = isset($stats[$id]) ? $stats[$id]['attempts_count'] : 0;
            $quiz['avg_score'] = isset($stats[$id]) ? $stats[$id]['avg_score'] : null;
        }

        return $quizzes;
    }

    private function getTotalAttempts()
    {
        $sql = "SELECT COUNT(*) AS total FROM quiz_attempts";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $row['total'];
    }
}
