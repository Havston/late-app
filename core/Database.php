<?php

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = Env::get('DB_HOST');
        $db   = Env::get('DB_NAME');
        $user = Env::get('DB_USER');
        $pass = Env::get('DB_PASS');

        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // исключения
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch по умолчанию
            PDO::ATTR_EMULATE_PREPARES => false,              // настоящие prepared statements
            PDO::ATTR_PERSISTENT => false                     // без persistent соединений
        ];

        try {

            $this->connection = new PDO($dsn, $user, $pass, $options);

        } catch (PDOException $e) {

            // логируем, но не показываем пользователю
            error_log($e->getMessage());

            die("Database connection error");

        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}