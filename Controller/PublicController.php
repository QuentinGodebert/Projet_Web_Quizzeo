<?php

session_start();

class PublicController
{
    private string $dataDir;
    private string $quizzesFile;
    private string $quizAttemptsFile;

    public function __construct()
    {
        $this->dataDir = __DIR__ . '/../data';
        $this->quizzesFile = $this->dataDir . '/quizzes.json';
        $this->quizAttemptsFile = $this->dataDir . '/quiz_attempts.json';
        $this->ensureDataFilesExist();
    }

    public function index(): void
    {
        require __DIR__ . '/../View/public/home.php';
    }

    public function startQuiz(): void
    {
        $token = $_GET['token'] ?? '';

        if ($token === '') {
            http_response_code(404);
            echo 'Quiz introuvable.';
            return;
        }

        $quiz = $this->findQuizByToken($token);

        if (!$quiz) {
            http_response_code(404);
            echo 'Quiz introuvable.';
            return;
        }

        if (!empty($quiz['isDisabled'])) {
            http_response_code(403);
            echo 'Ce quiz est désactivé.';
            return;
        }

        $questions = $quiz['questions'] ?? [];

        require __DIR__ . '/../View/quiz/start_quizz.php';
    }

    public function submitQuiz(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=public&action=index');
            exit;
        }

        $token = $_POST['token'] ?? '';

        if ($token === '') {
            http_response_code(404);
            echo 'Quiz introuvable.';
            return;
        }

        $quiz = $this->findQuizByToken($token);

        if (!$quiz) {
            http_response_code(404);
            echo 'Quiz introuvable.';
            return;
        }

        if (!empty($quiz['isDisabled'])) {
            http_response_code(403);
            echo 'Ce quiz est désactivé.';
            return;
        }

        $questions = $quiz['questions'] ?? [];
        $answersForm = $_POST['answers'] ?? [];

        $score = 0;
        $maxScore = 0;

        foreach ($questions as $question) {
            $questionId = $question['id'];
            $correctAnswerIds = [];

            foreach ($question['answers'] as $answer) {
                if (!empty($answer['isCorrect'])) {
                    $correctAnswerIds[] = $answer['id'];
                }
            }

            $userAnswerIds = $answersForm[$questionId] ?? [];

            if (!is_array($userAnswerIds)) {
                $userAnswerIds = [$userAnswerIds];
            }

            sort($correctAnswerIds);
            sort($userAnswerIds);

            $maxScore++;

            if ($correctAnswerIds === $userAnswerIds) {
                $score++;
            }
        }

        $quizAttempts = $this->loadJsonFile($this->quizAttemptsFile);

        $quizAttempts[] = [
            'quizId' => $quiz['id'],
            'date' => date('Y-m-d H:i:s'),
            'score' => $score,
            'maxScore' => $maxScore,
            'answers' => $answersForm
        ];

        $this->saveJsonFile($this->quizAttemptsFile, $quizAttempts);

        require __DIR__ . '/../View/quiz/end_quizz.php';
    }

    private function ensureDataFilesExist(): void
    {
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0777, true);
        }

        if (!file_exists($this->quizzesFile)) {
            file_put_contents($this->quizzesFile, json_encode([]));
        }

        if (!file_exists($this->quizAttemptsFile)) {
            file_put_contents($this->quizAttemptsFile, json_encode([]));
        }
    }

    private function loadJsonFile(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false || $content === '') {
            return [];
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    private function saveJsonFile(string $path, array $data): void
    {
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function findQuizByToken(string $token): ?array
    {
        $quizzes = $this->loadJsonFile($this->quizzesFile);

        foreach ($quizzes as $quiz) {
            if (isset($quiz['publicToken']) && $quiz['publicToken'] === $token) {
                return $quiz;
            }
        }

        return null;
    }
}
