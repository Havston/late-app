<?php

class Logger
{
    private static function write($level, $message)
    {
        $dir = __DIR__ . '/../storage/logs';
        $file = $dir . '/app.log';

        // создаём папку если её нет
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $date = date('Y-m-d H:i:s');

        $line = "[$date] [$level] $message" . PHP_EOL;

        file_put_contents(
            $file,
            $line,
            FILE_APPEND | LOCK_EX
        );
    }

    public static function info($message)
    {
        self::write('INFO', $message);
    }

    public static function warning($message)
    {
        self::write('WARNING', $message);
    }

    public static function error($message)
    {
        self::write('ERROR', $message);
    }
}