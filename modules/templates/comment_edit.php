<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Правка комментария</h2>
<?php require \Helpers\getFragmentPath('__comment_form') ?>
<?php $ret = '/' . $picture . \Helpers\getGETParams(['page', 'filter', 'ref']) ?>
<p><a href="<?php echo $ret ?>">Назад</a></p>
<?php require \Helpers\getFragmentPath('__footer') ?>