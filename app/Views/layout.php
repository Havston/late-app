<!DOCTYPE html>
<html lang="ru">

<head>

<meta charset="UTF-8">
<title>Late System</title>

<link rel="stylesheet" href="/assets/css/app.css">

</head>

<body>

<div class="layout">

<div class="sidebar">

<h2>Late System</h2>

<a href="/dashboard">Dashboard</a>
<a href="/late">Опоздания</a>
<a href="/register">Камера</a>
<a href="/reports">Статистика</a>

<hr>

<a href="/logout">Выход</a>

</div>


<div class="main">

<div class="header">

<div class="page-title">
<?= $title ?? '' ?>
</div>

<div class="user">
Админ
</div>

</div>


<div class="content">

<?= $content ?>

</div>

</div>

</div>

</body>
</html>