<?php

namespace Controllers;

class Login extends BaseController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $loginForm = \Forms\Login::getNormalizedData($_POST);

            if (!isset($loginForm['__errors'])) {
                $loginForm = \Forms\Login::getPreparedData($loginForm);
                $userId = \Forms\Login::verifyUser($loginForm);
                if ($userId) {
                    session_start();
                    $_SESSION['current_user'] = $userId;
                    \Helpers\redirect('/users/' . $loginForm['name']);
                }
            }
        } else {
            $loginForm = \Forms\Login::getInitialData();
        }
        $ctx = ['form' => $loginForm, 'site_title' => 'Вход'];
        $this->render('login', $ctx);
    }

    public function logout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            unset($_SESSION['current_user']);
            session_destroy();
            \Helpers\redirect('/');
        } else {
            $ctx = ['site_title' => 'Выход'];
            $this->render('logout', $ctx);
        }
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $regForm = \Forms\Register::getNormalizedData($_POST);
            if (!isset($regForm['__errors'])) {
                $regForm = \Forms\Register::getPreparedData($regForm);
                $users = new \Models\User();
                $users->insert($regForm);
                $values = [
                    'title' => \Settings\SITE_NAME,
                    'name' => $regForm['name'],
                    'url' => 'http://' . $_SERVER['SERVER_NAME'] .
                    '/users/' . $regForm['name'] .
                    '/account/activation/' .
                    hash_hmac('ripemd256', $regForm['name'],
                    \Settings\SECRET_KEY) . '/'
                ];
                \Helpers\sendMail($regForm['email'],
                    'activation_subject', 'activation_body',
                    $values);
                \Helpers\redirect('/register/complete/');
            }
        } else {
            $regForm = \Forms\Register::getInitialData([]);
        }
        $ctx = ['form' => $regForm, 'site_title' => 'Регистрация'];
        $this->render('register', $ctx);
    }

    public function registerComplete()
    {
        $ctx = ['site_title' => 'Регистрация завершена'];
        $this->render('register_complete', $ctx);
    }
}