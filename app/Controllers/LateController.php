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

    $studentName = trim($_POST['student_name'] ?? '');
    $text = trim($_POST['text'] ?? '');
    $date = $_POST['late_date'] ?? date('Y-m-d');

    if (!$studentName) {
        echo "Введите имя";
        return;
    }

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

    $studentName = substr(trim($data['student_name'] ?? ''), 0, 255);
    $text = substr(trim($data['text'] ?? ''), 0, 500);

    if (!$studentName) {
        http_response_code(400);
        echo json_encode(['error' => 'Имя не указано']);
        return;
    }

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

    $studentName = trim($_POST['student_name'] ?? '');
    $text = trim($_POST['text'] ?? '');
    $date = $_POST['late_date'] ?? date('Y-m-d');

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
}