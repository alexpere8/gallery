<?php http_response_code(503) ?>
<?php require \Helpers\getFragmentPath('__header') ?>
<h2>Внутренняя ошибка сервера</h2>
<pre><?php echo $message ?>
<?php echo $file, ', line ', $line ?></pre>
<p><a href="/">На главную</a></p>
<?php require \Helpers\getFragmentPath('__footer') ?>