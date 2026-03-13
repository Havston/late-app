<?php

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

header("Referrer-Policy: no-referrer");

header("Permissions-Policy: camera=(self), microphone=(self)");

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");

// В production ошибки не показываем пользователю
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// более безопасные параметры сессии
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Генерируем CSRF токен если его ещё нет
if (!isset($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// Подключаем автозагрузчик
require_once __DIR__ . '/../core/Autoloader.php';

// Загружаем env
$envPath = __DIR__ . '/../.env';

if (file_exists($envPath)) {
    Env::load($envPath);
} else {
    throw new Exception('.env file not found');
}

$router = new Router();

// Роуты
$router->get('/', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/dashboard', [DashboardController::class, 'index'], ['auth']);

$router->post('/logout', [AuthController::class, 'logout'], ['auth']);

$router->get('/admin', [AdminController::class, 'index'], ['auth', 'admin']);

$router->get('/late', [LateController::class, 'index'], ['auth']);
$router->post('/late/create', [LateController::class, 'create'], ['auth']);

$router->get('/register', [LateController::class, 'register'], ['auth']);

$router->post('/late/auto', [LateController::class, 'autoStore'], ['auth']);

$router->get('/late/edit', [LateController::class, 'edit'], ['auth']);
$router->post('/late/update', [LateController::class, 'update'], ['auth']);
$router->post('/late/delete', [LateController::class, 'delete'], ['auth']);

$router->get('/reports', [ReportsController::class, 'index'], ['auth']);

$router->get('/profile', [AuthController::class, 'profile'], ['auth']);
$router->post('/profile/password', [AuthController::class, 'changePassword'], ['auth']);

$router->get('/late/export', [LateController::class, 'export'], ['auth']);
$router->post('/late/exportDownload', [LateController::class, 'exportDownload'], ['auth']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);