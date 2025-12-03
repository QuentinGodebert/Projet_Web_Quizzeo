<?php
class SchoolController
{
    private $quizFile;
    private $responseFile;

    public function __construct()
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'school') {
            header('Location: /');
            exit;
        }

        $this->quizFile = __DIR__ . '/../data/quizzes.json';
        $this->responseFile = __DIR__ . '/../data/responses.json';

        if (!file_exists($this->quizFile)) {
            file_put_contents($this->quizFile, json_encode([]));
        }

        if (!file_exists($this->responseFile)) {
            file_put_contents($this->responseFile, json_encode([]));
        }
    }

    public function handle()
    {
        $action = $_GET['action'] ?? 'dashboard';

        switch ($action) {
            case 'create':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->store();
                } else {
                    $this->create();
                }
                break;

            case 'edit':
                if (!isset($_GET['id'])) {
                    header('Location: index.php?controller=school&action=dashboard');
                    exit;
                }
                $id = (int) $_GET['id'];
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->update($id);
                } else {
                    $this->edit($id);
                }
                break;

            case 'launch':
                $this->changeStatus((int) ($_GET['id'] ?? 0), 'launched');
                break;

            case 'finish':
                $this->changeStatus((int) ($_GET['id'] ?? 0), 'finished');
                break;

            case 'results':
                $this->results((int) ($_GET['id'] ?? 0));
                break;

            default:
                $this->dashboard();
                break;
        }
    }

    public function dashboard()
    {
        $userId = $_SESSION['user']['id'];
        $quizzes = $this->getQuizzesByOwner($userId);
        $responses = $this->loadResponses();

        foreach ($quizzes as &$quiz) {
            $quiz['responses_count'] = 0;
            foreach ($responses as $response) {
                if ($response['quiz_id'] === $quiz['id']) {
                    $quiz['responses_count']++;
                }
            }
        }

        $pageTitle = 'Dashboard école';
        require __DIR__ . '/../views/school/dashboard.php';
    }

    public function create()
    {
        $pageTitle = 'Créer un quiz';
        require __DIR__ . '/../views/school/create.php';
    }

    public function store()
    {
        if (empty($_POST['title'])) {
            $_SESSION['flash_error'] = 'Le titre du quiz est obligatoire.';
            header('Location: index.php?controller=school&action=create');
            exit;
        }

        $quizzes = $this->loadQuizzes();

        $nextId = 1;
        foreach ($quizzes as $quiz) {
            if ($quiz['id'] >= $nextId) {
                $nextId = $quiz['id'] + 1;
            }
        }

        $quiz = [
            'id' => $nextId,
            'owner_id' => $_SESSION['user']['id'],
            'title' => trim($_POST['title']),
            'status' => 'draft',
            'questions' => [],
            'created_at' => date('Y-m-d H:i:s')
        ];

        $quizzes[] = $quiz;
        $this->saveQuizzes($quizzes);

        header('Location: index.php?controller=school&action=edit&id=' . $nextId);
        exit;
    }

    public function edit(int $id)
    {
        $quiz = $this->findQuizForCurrentSchool($id);

        if (!$quiz) {
            http_response_code(404);
            echo 'Quiz introuvable';
            exit;
        }

        $pageTitle = 'Éditer le quiz';
        require __DIR__ . '/../views/school/edit.php';
    }

    public function update(int $id)
    {
        $quizzes = $this->loadQuizzes();
        $foundIndex = null;

        foreach ($quizzes as $index => $quiz) {
            if ($quiz['id'] === $id && $quiz['owner_id'] === $_SESSION['user']['id']) {
                $foundIndex = $index;
                break;
            }
        }

        if ($foundIndex === null) {
            http_response_code(404);
            echo 'Quiz introuvable';
            exit;
        }

        if (!empty($_POST['title'])) {
            $quizzes[$foundIndex]['title'] = trim($_POST['title']);
        }

        $questions = [];

        if (!empty($_POST['question_label']) && is_array($_POST['question_label'])) {
            $labels = $_POST['question_label'];
            $points = $_POST['question_points'] ?? [];
            $choice1 = $_POST['choice_1'] ?? [];
            $choice2 = $_POST['choice_2'] ?? [];
            $choice3 = $_POST['choice_3'] ?? [];
            $choice4 = $_POST['choice_4'] ?? [];
            $correct = $_POST['correct_choice'] ?? [];

            $nextQuestionId = 1;

            foreach ($labels as $i => $label) {
                $label = trim($label);
                if ($label === '') {
                    continue;
                }

                $qPoints = isset($points[$i]) ? (int) $points[$i] : 1;
                if ($qPoints <= 0) {
                    $qPoints = 1;
                }

                $choices = [
                    trim($choice1[$i] ?? ''),
                    trim($choice2[$i] ?? ''),
                    trim($choice3[$i] ?? ''),
                    trim($choice4[$i] ?? '')
                ];

                $choices = array_values(array_filter($choices, function ($c) {
                    return $c !== '';
                }));

                if (count($choices) < 2) {
                    continue;
                }

                $correctIndex = isset($correct[$i]) ? (int) $correct[$i] : 0;
                if ($correctIndex < 0 || $correctIndex >= count($choices)) {
                    $correctIndex = 0;
                }

                $questions[] = [
                    'id' => $nextQuestionId,
                    'label' => $label,
                    'points' => $qPoints,
                    'choices' => $choices,
                    'correct_index' => $correctIndex
                ];

                $nextQuestionId++;
            }
        }

        $quizzes[$foundIndex]['questions'] = $questions;
        $this->saveQuizzes($quizzes);

        $_SESSION['flash_success'] = 'Quiz mis à jour.';
        header('Location: index.php?controller=school&action=edit&id=' . $id);
        exit;
    }

    public function changeStatus(int $id, string $status)
    {
        if (!in_array($status, ['draft', 'launched', 'finished'], true)) {
            header('Location: index.php?controller=school&action=dashboard');
            exit;
        }

        $quizzes = $this->loadQuizzes();
        $changed = false;

        foreach ($quizzes as &$quiz) {
            if ($quiz['id'] === $id && $quiz['owner_id'] === $_SESSION['user']['id']) {
                $quiz['status'] = $status;
                $changed = true;
                break;
            }
        }

        if ($changed) {
            $this->saveQuizzes($quizzes);
        }

        header('Location: index.php?controller=school&action=dashboard');
        exit;
    }

    public function results(int $id)
    {
        $quiz = $this->findQuizForCurrentSchool($id);

        if (!$quiz) {
            http_response_code(404);
            echo 'Quiz introuvable';
            exit;
        }

        if ($quiz['status'] !== 'finished') {
            header('Location: index.php?controller=school&action=dashboard');
            exit;
        }

        $responses = $this->loadResponses();
        $quizResponses = [];

        foreach ($responses as $response) {
            if ($response['quiz_id'] === $quiz['id']) {
                $quizResponses[] = $response;
            }
        }

        $pageTitle = 'Résultats du quiz';
        require __DIR__ . '/../views/school/results.php';
    }

    private function loadQuizzes(): array
    {
        $content = file_get_contents($this->quizFile);
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    private function saveQuizzes(array $quizzes): void
    {
        file_put_contents($this->quizFile, json_encode($quizzes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function loadResponses(): array
    {
        $content = file_get_contents($this->responseFile);
        $data = json_decode($content, true);

        if (!is_array($data)) {
            return [];
        }

        return $data;
    }

    private function getQuizzesByOwner(int $ownerId): array
    {
        $quizzes = $this->loadQuizzes();
        $result = [];

        foreach ($quizzes as $quiz) {
            if ($quiz['owner_id'] === $ownerId) {
                $result[] = $quiz;
            }
        }

        return $result;
    }

    private function findQuizForCurrentSchool(int $id): ?array
    {
        $quizzes = $this->loadQuizzes();

        foreach ($quizzes as $quiz) {
            if ($quiz['id'] === $id && $quiz['owner_id'] === $_SESSION['user']['id']) {
                return $quiz;
            }
        }

        return null;
    }
}

$controller = new SchoolController();
$controller->handle();
