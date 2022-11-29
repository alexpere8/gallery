<?php

namespace Forms;

class Password extends \Forms\Form
{
    protected const FIELDS = [
        'password1' => ['type' => 'string', 'nosave' => TRUE],
        'password2' => ['type' => 'string', 'nosave' => TRUE]
    ];

    protected static function afterNormalizeData(&$data, &$errors)
    {
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