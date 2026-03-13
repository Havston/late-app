<h2>Опоздания</h2>

<div class="card">

<h3>Добавить опоздание</h3>

<form method="POST" action="/late/create" class="form-grid">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<div class="form-full">
<label>Текст</label>
<input type="text" name="text" required>
</div>

<div>
<label>Дата</label>
<input type="date" name="late_date" required>
</div>

<div class="form-full">
<button type="submit" class="btn btn-primary">Добавить</button>
</div>

</form>

</div>



<div class="card">

<a href="/late/export" class="btn btn-primary">
Скачать CSV
</a>

</div>



<div class="card">

<h3>Поиск</h3>

<form method="GET" action="/late" class="search-box">

<input
type="text"
name="search"
placeholder="Поиск по ученику..."
value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
>

<button type="submit" class="btn btn-primary">Поиск</button>

<a href="/late" class="btn btn-reset">Сбросить</a>

</form>

</div>



<div class="card">

<h3>Список опозданий</h3>

<?php if (empty($records)): ?>

<p>Записей пока нет.</p>

<?php else: ?>

<table class="table">

<thead>
<tr>
<th>Дата</th>
<th>Ученик</th>
<th>Текст</th>
<th>Действия</th>
</tr>
</thead>

<tbody>

<?php foreach ($records as $record): ?>

<tr>

<td><?= htmlspecialchars($record['late_date'], ENT_QUOTES, 'UTF-8') ?></td>

<td><?= htmlspecialchars($record['student_name'], ENT_QUOTES, 'UTF-8') ?></td>

<td><?= htmlspecialchars($record['text'], ENT_QUOTES, 'UTF-8') ?></td>

<td class="actions">

<a class="btn btn-warning btn-sm"
href="/late/edit?id=<?= (int)$record['id'] ?>">✏</a>

<form method="POST" action="/late/delete" style="display:inline">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<input type="hidden" name="id" value="<?= (int)$record['id'] ?>">

<button
class="btn btn-danger btn-sm"
onclick="return confirm('Удалить запись?')"
>
🗑
</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php endif; ?>

</div>