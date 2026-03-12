<?php

class AuthController
{
    public function showLogin()
    {
        View::render('login');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /");
            exit;
        }

        // CSRF проверка
        if (!isset($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
            die('CSRF validation failed');
        }

        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$login || !$password) {
            View::render('login', [
                'error' => 'Заполните все поля'
            ]);
            return;
        }

        $db = Database::getInstance()->getConnection();

        $stmt = $db->prepare("
            SELECT users.*, schools.name AS school_name
            FROM users
            JOIN schools ON users.school_id = schools.id
            WHERE users.login = ?
            LIMIT 1
        ");

        $stmt->execute([$login]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {

            View::render('login', [
                'error' => 'Неверный логин или пароль'
            ]);

            return;
        }

        // защита от session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'school_id' => $user['school_id'],
            'login' => $user['login'],
            'role' => $user['role'],
            'school_name' => $user['school_name']
        ];

        header("Location: /dashboard");
        exit;
    }

    public function logout()
    {
        $_SESSION = [];

        session_destroy();

        header("Location: /");
        exit;
    }

    public function changePassword()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $userId = $_SESSION['user']['id'];

    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $repeat = $_POST['repeat_password'] ?? '';

    if (!$old || !$new || !$repeat) {
        die("Заполните все поля");
    }

    if ($new !== $repeat) {
        die("Пароли не совпадают");
    }

    $db = Database::get();

    $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    $user = $stmt->fetch();

    if (!$user) {
        die("Пользователь не найден");
    }

    if (!password_verify($old, $user['password'])) {
        die("Старый пароль неверный");
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);

    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $userId]);

    echo "Пароль изменён";
}

public function profile()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $user = $_SESSION['user'];

    View::render('profile', [
        'user' => $user
    ]);
}
}