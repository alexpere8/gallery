<?php

namespace Forms;

class Register extends \Forms\Form
{
    protected const FIELDS = [
        'name' => ['type' => 'string'],
        'password1' => ['type' => 'string', 'nosave' => TRUE],
        'password2' => ['type' => 'string', 'nosave' => TRUE],
        'email' => ['type' => 'email'],
        'name1' => ['type' => 'string', 'optional' => TRUE],
        'name2' => ['type' => 'string', 'optional' => TRUE],
        'emailme' => ['type' => 'boolean', 'initial' => TRUE]
    ];

    protected static function afterNormalizeData(&$data, &$errors)
    {
        $notAllowUserName = $data['name'] && !preg_match('/[a-zA-Z0-9_-]{5,20}/', $data['name']);
        if ($notAllowUserName) {
            $errors['name'] = 'Имя пользователя должно включать ' .
                'лишь латинские буквы, цифры, дефисы, символы ' .
                'подчеркивания и содержать от 5 до 20 знаков';
        }
        $users = new \Models\User();
        if ($users->get($data['name'], 'name', 'id')) {
            $errors['name'] = 'Пользователь с таким именем уже ' .
                'существует';
        }
        if ($data['password1'] != $data['password2']) {
            $errors['password2'] = 'Введите в эти поля один и тот ' .
                'же пароль';
        }
    }

    protected static function afterPrepareData(&$data, &$normData)
    {
        $data['password'] = password_hash($normData['password1'],
            PASSWORD_BCRYPT);
    }
}