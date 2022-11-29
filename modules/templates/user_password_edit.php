<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Регистрация</h2>
<form class="bigform" method="post">
    <input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
    <label for="user_password1">Пароль</label>
    <input type="password" id="user_password1" name="password1">
    <?php \Helpers\showErrors('password1', $form) ?>
    <label for="user_password2">Подтверждение пароля</label>
    <input type="password" id="user_password2" name="password2">
    <?php \Helpers\showErrors('password2', $form) ?>
    <input type="submit" value="Отправить">
</form>
<?php require \Helpers\getFragmentPath('__footer') ?>