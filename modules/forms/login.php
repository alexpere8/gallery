<?php

namespace Forms;

class Login extends \Forms\Form
{
    protected const FIELDS = [
        'name' => ['type' => 'string'],
        'password' => ['type' => 'string']
    ];

    static function verifyUser(&$data)
    {
        $errors = [];
        $users = new \Models\User();
        $user = $users->get($data['name'], 'name', 'id, password, active');
        if (!$user) {
            $errors['name'] = 'Неверное имя пользователя';
        } else {
            if (!$user['active']) {
                $errors['name'] = 'Этот пользователь неактивен';
            } else {
                $isVerify = password_verify($data['password'], $user['password']);
                if (!$isVerify) {
                    $errors['password'] = 'Неверный пароль';
                } else {
                    return $user['id'];
                }
            }
        }
        $data['__errors'] = $errors;
        return FALSE;
    }
}