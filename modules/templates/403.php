<?php http_response_code(403) ?>
<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Доступ запрещен</h2>
<p>У вас нет прав на доступ к этой странице или
выполнения этой операции.</p>
<p><a href="/">На главную</a></p>
<?php require \Helpers\getFragmentPath('__footer') ?>