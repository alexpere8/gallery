<?php require \Helpers\getFragmentPath('__header') ?>
<?php $ret = '/' . $picture . \Helpers\getGETParams(['page', 'filter', 'ref']) ?>
<h2>Правка комментария</h2>
<p>Оставлен пользователем: <?php echo $comment['user_name'] ?></p>
<p><?php echo $comment['contents'] ?></p>
<p>Опубликован:
<?php echo \Helpers\getFormattedTimestamp($comment['uploaded']) ?></p>
<form method="post">
    <input type="hidden" name="__token" value="<?php echo $__token ?>">
    <input type="submit" value="Удалить">
</form>
<p><a href="<?php echo $ret ?>">Назад</a></p>
<?php require \Helpers\getFragmentPath('__footer') ?>