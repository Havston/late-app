<h2>Статистика опозданий</h2>

<div class="card">
<h3>Общая статистика</h3>

<p>Всего опозданий: <b><?= (int)($stats['total'] ?? 0) ?></b></p>
<p>Сегодня: <b><?= (int)($stats['today'] ?? 0) ?></b></p>

</div>


<div class="card">
<h3>Топ учеников</h3>

<?php if (!empty($stats['top_students'])): ?>

<table class="table">

<thead>
<tr>
<th>Ученик</th>
<th>Опозданий</th>
</tr>
</thead>

<tbody>

<?php foreach ($stats['top_students'] as $s): ?>

<tr>
<td><?= htmlspecialchars($s['student_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
<td><?= (int)($s['count'] ?? 0) ?></td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php else: ?>

<p>Нет данных.</p>

<?php endif; ?>

</div>