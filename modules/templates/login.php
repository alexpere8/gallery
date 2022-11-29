<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Вход</h2>
<div class="login-content">
    <form class="bigform" method="post">
        <label for="user_name">Имя</label>
        <input type="text" id="user_name" name="name" value="<?php echo $form['name'] ?>">
        <?php \Helpers\showErrors('name', $form) ?>
        <label for="user_password">Пароль</label>
        <input type="password" id="user_password" name="password">
        <?php \Helpers\showErrors('password', $form) ?>
        <input type="submit" value="Отправить">
    </form>
</div>
<?php require \Helpers\getFragmentPath('__footer') ?>