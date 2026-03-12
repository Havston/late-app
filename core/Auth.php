<?php

class Auth
{
    public static function attempt($login, $password)
    {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
        $stmt->execute([$login]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // защита от session fixation
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'school_id' => (int)$user['school_id'],
            'role' => $user['role']
        ];

        return true;
    }

    public static function check()
    {
        return isset($_SESSION['user']) && is_array($_SESSION['user']);
    }

    public static function school()
    {
        return $_SESSION['user']['school_id'] ?? null;
    }

    public static function user()
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role()
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function logout()
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {

            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }
}