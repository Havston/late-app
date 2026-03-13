<?php

class LateController
{
    private function checkCsrf()
    {
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
            die('CSRF validation failed');
        }
    }

    private function requireAuth()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit;
        }
    }

    private function extractName($text)
    {
        $words = explode(" ", trim($text));

        $first = $words[0] ?? '';
        $second = $words[1] ?? '';

        return trim($first . " " . $second);
    }


    public function index()
    {
        $this->requireAuth();

        $model = new LateRecord();

        $search = trim($_GET['search'] ?? '');
        $search = substr($search, 0, 100);

        $records = $model->getBySchool(
            (int)$_SESSION['user']['school_id'],
            $search
        );

        View::render('late_list', [
            'records' => $records
        ]);
    }


    public function register()
    {
        $this->requireAuth();
        View::render('register');
    }


    public function create()
    {
        $this->requireAuth();
        $this->checkCsrf();

        $text = trim($_POST['text'] ?? '');
        $date = $_POST['late_date'] ?? date('Y-m-d');

        if (!$text) {
            echo "Введите текст";
            return;
        }

        $studentName = $this->extractName($text);

        $model = new LateRecord();

        $model->create(
            (int)$_SESSION['user']['school_id'],
            $studentName,
            $text,
            $date
        );

        header("Location: /late");
        exit;
    }


    public function autoStore()
    {
        $this->requireAuth();

        $data = json_decode(file_get_contents("php://input"), true);

        $text = substr(trim($data['text'] ?? ''), 0, 500);

        if (!$text) {
            http_response_code(400);
            echo json_encode(['error' => 'Нет текста']);
            return;
        }

        $studentName = $this->extractName($text);

        $model = new LateRecord();

        $model->create(
            (int)$_SESSION['user']['school_id'],
            $studentName,
            $text,
            date('Y-m-d')
        );

        echo json_encode(['success' => true]);
    }


    public function delete()
    {
        $this->requireAuth();
        $this->checkCsrf();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header("Location: /late");
            exit;
        }

        $model = new LateRecord();

        $model->delete(
            $id,
            (int)$_SESSION['user']['school_id']
        );

        Logger::log(
            "User {$_SESSION['user']['id']} deleted late record #$id"
        );

        header("Location: /late");
        exit;
    }


    public function edit()
    {
        $this->requireAuth();

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header("Location: /late");
            exit;
        }

        $model = new LateRecord();

        $record = $model->getById(
            $id,
            (int)$_SESSION['user']['school_id']
        );

        if (!$record) {
            http_response_code(403);
            echo "Доступ запрещён";
            exit;
        }

        View::render('late_edit', [
            'record' => $record
        ]);
    }


    public function update()
    {
        $this->requireAuth();
        $this->checkCsrf();

        $id = (int)($_POST['id'] ?? 0);

        $text = trim($_POST['text'] ?? '');
        $date = $_POST['late_date'] ?? date('Y-m-d');

        $studentName = $this->extractName($text);

        $model = new LateRecord();

        $model->update(
            $id,
            $studentName,
            $text,
            $date,
            (int)$_SESSION['user']['school_id']
        );

        header("Location: /late");
        exit;
    }

    public function export()
    {
    $this->requireAuth();

    $model = new LateRecord();

    $records = $model->getBySchool(
        (int)$_SESSION['user']['school_id'],
        ''
    );

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=late.csv');

    $f = fopen('php://output', 'w');

    fputcsv($f, ['date', 'name', 'text']);

    foreach ($records as $r) {

        fputcsv($f, [
            $r['late_date'],
            $r['student_name'],
            $r['text']
        ]);

    }

    fclose($f);
    exit;
    }
}