<h2>Редактировать опоздание</h2>

<div class="card">

<form method="POST" action="/late/update" class="form-grid">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<input type="hidden" name="id" value="<?= (int)$record['id'] ?>">

<div>
<label>ФИО ученика</label>
<input
type="text"
name="student_name"
value="<?= htmlspecialchars($record['student_name'], ENT_QUOTES, 'UTF-8') ?>"
required
>
</div>

<div>
<label>Класс</label>
<input
type="text"
name="class_name"
value="<?= htmlspecialchars($record['class_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
>
</div>

<div>
<label>Причина</label>
<input
type="text"
name="reason"
value="<?= htmlspecialchars($record['reason'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
>
</div>

<div>
<label>Дата</label>
<input
type="date"
name="late_date"
value="<?= htmlspecialchars($record['late_date'], ENT_QUOTES, 'UTF-8') ?>"
required
>
</div>

<div class="form-full">
<button class="btn btn-primary">Сохранить</button>
</div>

</form>

</div>