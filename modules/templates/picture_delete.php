<?php require \Helpers\getFragmentPath('__header') ?>
<?php $ref = '/users/' . $username . \Helpers\getGETParams(['page', 'filter']) ?>
<h2>Удаление изображения</h2>
<p><?php echo $picture['title'] ?></p>
<p>Опубликовано пользователем: <?php echo $username ?></p>
<p>Дата и время публикации:
<?php echo \Helpers\getFormattedTimestamp($picture['uploaded']) ?>
</p>
<form method="post">
    <input type="hidden" name="__token" value="<?php echo $__token ?>">
    <input type="submit" value="Удалить">
</form>
<p><a href="<?php echo $ref ?>">Назад</a></p>
<?php require \Helpers\getFragmentPath('__footer') ?>