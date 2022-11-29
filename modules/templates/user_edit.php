<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Правка сведений о пользователе</h2>
<form class="bigform" method="post">
    <input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
    <label for="user_email">Адрес электронной почты</label>
    <input type="text" id="user_email" name="email" value="<?php echo $form['email'] ?>">
    <?php \Helpers\showErrors('email', $form) ?>
    <label for="user_name1">Настоящее имя</label>
    <input type="text" id="user_name1" name="name1" value="<?php echo $form['name1'] ?>">
    <?php \Helpers\showErrors('name1', $form) ?>
    <label for="user_name2">Настоящая фамилия</label>
    <input type="text" id="user_name2" name="name2" value="<?php echo $form['name2'] ?>">
    <?php \Helpers\showErrors('name2', $form) ?>
    <label for="user_emailme">Получать оповещения о
    новых комментариях?</label>
    <input type="checkbox" id="user_emailme" name="emailme" value="1" <?php if ($form['emailme']) echo 'checked' ?>>
    <input type="submit" value="Отправить">
</form>
<?php require \Helpers\getFragmentPath('__footer') ?>