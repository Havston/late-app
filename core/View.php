<?php

class View
{
    public static function render($view, $data = [])
    {
        $viewFile = __DIR__ . '/../app/Views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../app/Views/layout.php';

        // проверка существования view
        if (!is_file($viewFile)) {
            throw new Exception("View not found: $view");
        }

        // извлекаем переменные
        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // проверка layout
        if (!is_file($layoutFile)) {
            throw new Exception("Layout not found");
        }

        require $layoutFile;
    }
}