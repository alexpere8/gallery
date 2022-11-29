<?php

namespace Models;

class User extends \Models\Model
{
    protected const TABLE_NAME = 'users';
    protected const DEFAULT_ORDER = 'name';

    protected function beforeDelete($value, $keyField = 'id')
    {
        if ($keyField != 'id') {
            $users = new \Models\User();
            $user = $users->get($value, $keyField, 'id');
            $value = $user['id'];
        }
        $pictures = new \MODELS\Picture();
        $pictures2 = new \MODELS\Picture();
        $pictures->select('id', NULL, 'user = ?', [$value]);
        foreach ($pictures as $picture) {
            $pictures2->delete($picture['id']);
        }
    }
}