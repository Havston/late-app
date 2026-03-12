<div class="card">

<h2>Аккаунт</h2>

<p>Логин: <?= htmlspecialchars($user['login']) ?></p>
<p>School ID: <?= htmlspecialchars($user['school_id']) ?></p>

<hr>

<h3>Смена пароля</h3>

<form method="post" action="/profile/password">

<div class="form-grid">

    <div>
        <label>Старый пароль</label>
        <input type="password" name="old_password">
    </div>

    <div>
        <label>Новый пароль</label>
        <input type="password" name="new_password">
    </div>

    <div>
        <label>Повторите пароль</label>
        <input type="password" name="repeat_password">
    </div>

    <div class="form-full">

        <button class="btn btn-primary">
            Сменить пароль
        </button>

    </div>

</div>

</form>

</div>