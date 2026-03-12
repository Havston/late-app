<?php

class DashboardController
{
    public function index()
    {
        // Проверка авторизации (если middleware вдруг не отработает)
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit;
        }

        $schoolId = (int) ($_SESSION['user']['school_id'] ?? 0);

        $model = new LateRecord();
        $todayCount = $model->countToday($schoolId);

        View::render('dashboard', [
            'todayCount' => $todayCount
        ]);
    }
}