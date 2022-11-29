<?php

namespace Models;

class Picture extends \Models\Model
{
    protected const TABLE_NAME = 'pictures';
    protected const DEFAULT_ORDER = 'uploaded DESC';
    protected const RELATIONS =
        ['categories' => ['external' => 'category', 'primary' => 'id'],
                 'users' => ['external' => 'user', 'primary' => 'id']];
    
    protected function beforeInsert(&$fields)
    {
        $filename = \Helpers\saveFile($_FILES['picture']);
        $fields['filename'] = $filename;
    }

    protected function beforeUpdate(&$fields, $value, $keyField = 'id')
    {
        if ($_FILES['picture']['error'] != UPLOAD_ERR_NO_FILE) {
            $this->beforeDelete($value, $keyField);
            $this->beforeInsert($fields);
        }
    }

    protected function beforeDelete($value, $keyField = 'id')
    {
        $rec = $this->getOr404($value, $keyField, 'filename');
        \Helpers\deleteFile($rec['filename']);
        \Helpers\deleteThumbnail($rec['filename']);
    }
}