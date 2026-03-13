<h2>Опоздания</h2>

<div class="card">

<h3>Добавить опоздание</h3>

<form method="POST" action="/late/create">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<label>Текст</label>
<input type="text" name="text" required>

<label>Дата</label>
<input type="date" name="late_date" required>

<button type="submit">Добавить</button>

</form>

</div>


<div class="card">

<a href="/late/export" class="btn btn-primary">
Скачать CSV
</a>

</div>


<div class="card">

<h3>Поиск</h3>

<form method="GET" action="/late">

<input
type="text"
name="search"
value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
>

<button type="submit">Поиск</button>

</form>

</div>


<div class="card">

<h3>Список</h3>

<?php if (empty($records)): ?>

Нет записей

<?php else: ?>

<table>

<tr>
<th>Дата</th>
<th>Ученик</th>
<th>Текст</th>
<th></th>
</tr>

<?php foreach ($records as $r): ?>

<tr>

<td><?= htmlspecialchars($r['late_date']) ?></td>

<td><?= htmlspecialchars($r['student_name']) ?></td>

<td><?= htmlspecialchars($r['text']) ?></td>

<td>

<a href="/late/edit?id=<?= $r['id'] ?>">edit</a>

<form method="POST" action="/late/delete">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
<input type="hidden" name="id" value="<?= $r['id'] ?>">

<button>delete</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</table>

<?php endif; ?>

</div>