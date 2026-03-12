<?php

class ReportsController
{
    public function index()
    {
        // защита если пользователь не авторизован
        if (!isset($_SESSION['user'])) {
            header("Location: /");
            exit;
        }

        $schoolId = (int) ($_SESSION['user']['school_id'] ?? 0);

        $model = new LateRecord();

        $stats = $model->getStats($schoolId);

        View::render('reports', [
            'stats' => $stats
        ]);
    }
}