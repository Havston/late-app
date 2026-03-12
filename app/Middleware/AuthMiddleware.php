<?php

class AuthMiddleware
{
    public function handle()
    {
        // если сессия вдруг не запущена
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // проверка авторизации
        if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {
            header("Location: /");
            exit;
        }

        // дополнительная проверка обязательных полей
        if (!isset($_SESSION['user']['id'], $_SESSION['user']['school_id'])) {
            session_destroy();
            header("Location: /");
            exit;
        }
    }
}