<?php

class Env
{
    public static function load($path)
    {
        if (!is_file($path)) {
            throw new Exception(".env file not found");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            $line = trim($line);

            // пропускаем комментарии
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            // если нет "=" — пропускаем строку
            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);

            $key = trim($key);
            $value = trim($value);

            // убираем кавычки
            $value = trim($value, "\"'");

            $_ENV[$key] = $value;

            putenv("$key=$value");
        }
    }

    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}