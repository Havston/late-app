<div class="login-wrapper">

<div class="login-card">

<h2>Вход в систему</h2>

<?php if (!empty($error)): ?>
<p class="login-error">
<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
</p>
<?php endif; ?>

<form method="POST" action="/login">

<input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

<div class="login-field">
<label>Логин</label>
<input
type="text"
name="login"
required
maxlength="50"
autocomplete="username"
>
</div>

<div class="login-field">
<label>Пароль</label>
<input
type="password"
name="password"
required
maxlength="100"
autocomplete="current-password"
>
</div>

<button class="btn btn-primary login-btn">
Войти
</button>

</form>

</div>

</div>