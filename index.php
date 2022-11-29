<?php
$basePath = __DIR__ . '\\';
require_once $basePath . 'modules\settings.php';

function PHGAutoloader(string $className)
{
    global $basePath;
    require_once $basePath . 'modules\\' . $className . '.php';
}
spl_autoload_register('PHGAutoloader');
    
function exceptionHandler($error)
{
    $ctrError = new \Controllers\Error();
    if ($error instanceof Page404Exception) {
        $ctrError->page404();
    } elseif ($error instanceof Page403Exception) {
        $ctrError->page403();
    } else {
        $ctrError->page503($error);
    }
}
set_exception_handler('exceptionHandler');

require_once $basePath . 'modules\router.php';