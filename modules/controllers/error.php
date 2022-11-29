<?php

namespace Controllers;

class Error extends BaseController
{
    public function page404()
    {
        $this->render('404', []);
    }

    public function page503($error)
    {
        $ctx = [
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine()
            ];
        $this->render('503', $ctx);
    }

    public function page403()
    {
        $this->render('403', []);
    }
}