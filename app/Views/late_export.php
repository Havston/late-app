<h2>Экспорт CSV</h2>

<form method="POST" action="/late/exportDownload">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<table class="table">

<tr>
<th></th>
<th>Дата</th>
<th>Ученик</th>
<th>Текст</th>
</tr>

<?php foreach ($records as $r): ?>

<tr>

<td>
<input type="checkbox" name="ids[]" value="<?= $r['id'] ?>">
</td>

<td><?= htmlspecialchars($r['late_date']) ?></td>

<td><?= htmlspecialchars($r['student_name']) ?></td>

<td><?= htmlspecialchars($r['text']) ?></td>

</tr>

<?php endforeach; ?>

</table>

<button class="btn btn-primary">
Скачать CSV
</button>

</form>