<?php

namespace Controllers;

class Account extends BaseController
{
    private function checkUser(string $userName)
    {
        $users = new \Models\User();
        $user = $users->getOr404($userName, 'name', 'id');
        $userExistsAndValid = $this->currentUser;
        $userValid = $this->currentUser['id'] == $user['id'] || $this->currentUser['admin'];

        if (!($userExistsAndValid && $userValid)) {
            throw new \Page403Exception();
        }
    }

    public function deleteAccount(string $userName)
    {
        $this->checkUser($userName);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $users = new \Models\User();
            $users->delete($userName, 'name');
            unset ($_SESSION['current_user']);
            session_destroy();
            \Helpers\redirect('/');
        } else {
            $token = \Helpers\generateToken();
            $ctx = [
                'site_title' => 'Удаление пользователя',
                '__token' => $token
            ];
            $this->render('user_delete', $ctx);
        }
    }

    public function editAccount(string $userName)
    {
        $this->checkUser($userName);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $userForm = \Forms\User::getNormalizedData($_POST);
            if (!isset($Helpers['__errors'])) {
                $userForm = \Forms\User::getPreparedData($userForm);
                $users = new \Models\User();
                $users->update($userForm, $userName, 'name');
                \Helpers\redirect('/users/' . $userName);
            }
        } else {
            $users = new \Models\User();
            $user = $users->getOr404($userName, 'name');
            $userForm = \Forms\User::getInitialData($user);
        }
        $userForm['__token'] = \Helpers\generateToken();
        $ctx = [
            'form' => $userForm,
            'site_title' => 'Правка пользователя'
        ];
        $this->render('user_edit', $ctx);
    }

    public function editPassword(string $userName)
    {
        $this->checkUser($userName);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $passwordForm = \Forms\Password::getNormalizedData($_POST);
            if (!isset($passwordForm['__errors'])) {
                $passwordForm = \Forms\Password::getPreparedData($passwordForm);
                $users = new \Models\User();
                $users->update($passwordForm, $userName, 'name');
                \Helpers\redirect('/users/' . $userName);
            }
        } else {
            $passwordForm = \Forms\Password::getInitialData();
        }
        $passwordForm['__token'] = \Helpers\generateToken();
        $ctx = [
            'form' => $passwordForm,
            'site_title' => 'Правка пароля пользователя'
        ];
        $this->render('user_password_edit', $ctx);
    }

    public function activate(string $userName, string $token)
    {
        $users = new \Models\user();
        $user = $users->get($userName, 'name');
        $messageStr = 'Пользователя нет в списке';
        if ($user) {
            $messageStr = 'Пользователь уже активирован';
            if (!$user['active']) {
                $realToken = hash_hmac('ripemd256', $userName, \Settings\SECRET_KEY);
                if ($realToken != $token) {
                    $messageStr = 'Неверный интернет-адрес';
                } else {
                    $users->update(['active' => TRUE], $user['id']);
                    $messageStr = 'Активация прошла успешно';
                }
            }
        }
        $ctx = [
            'message' => $messageStr,
            'site_title' => 'Активация пользователя'
        ];
        $this->render('activate', $ctx);
    }
}