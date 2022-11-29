<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Добавление изображения</h2>
<?php require \Helpers\getFragmentPath('__picture_form') ?>
<?php $ret = '/users/' . $username .
    \Helpers\getGETParams(['page', 'filter']) ?>
<p><a href="<?php echo $ret ?>">Назад</a></p>
<?php require \Helpers\getFragmentPath('__footer') ?>