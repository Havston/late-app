<h2>Статистика</h2>

<div>

Всего: <?= $stats['total'] ?>

Сегодня: <?= $stats['today'] ?>

</div>


<h3>Топ учеников</h3>

<table>

<tr>
<th>Имя</th>
<th>Кол-во</th>
</tr>

<?php foreach ($stats['top_students'] as $s): ?>

<tr>

<td><?= htmlspecialchars($s['student_name']) ?></td>

<td><?= $s['count'] ?></td>

</tr>

<?php endforeach; ?>

</table>