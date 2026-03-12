<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>

<meta charset="UTF-8">
<title>Late System</title>

<link rel="stylesheet" href="/assets/css/app.css">

</head>

<body>

<?php if (isset($_SESSION['user'])): ?>

<div class="layout">

    <div class="sidebar">

        <h2>Late System</h2>

        <a href="/dashboard">Dashboard</a>
        <a href="/late">Опоздания</a>
        <a href="/register">Камера</a>
        <a href="/reports">Статистика</a>
        <a href="/profile">Аккаунт</a>

        <hr>

        <form method="post" action="/logout">
            <button class="btn btn-danger">Выход</button>
        </form>

    </div>


    <div class="main">

        <div class="header">

            <div class="page-title">
                <?= $title ?? '' ?>
            </div>

            <div class="user">
                <?= $_SESSION['user']['login'] ?? '' ?>
            </div>

        </div>


        <div class="content">

            <?= $content ?>

        </div>

    </div>

</div>

<?php else: ?>

<?= $content ?>

<?php endif; ?>

</body>
</html>