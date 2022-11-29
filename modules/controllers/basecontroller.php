<?php

namespace Controllers;

require_once $basePath . 'modules\helpers.php';

class BaseController
{
    public $currentUser = NULL;

    protected function contextAppend(array &$context)
    {
        $context['__current_user'] = $this->currentUser;
    }

    protected function render(string $template, array $context)
    {
        $this->contextAppend($context);
        \Helpers\render($template, $context);
    }

    public function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (isset($_SESSION['current_user'])) {
            $users = new \Models\User();
            $this->currentUser = $users->getOr404($_SESSION['current_user']);
        } else {
            session_destroy();
        }
    }
}