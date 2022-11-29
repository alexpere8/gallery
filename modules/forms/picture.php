<?php

namespace Forms;

class Picture extends \Forms\Form
{
    protected const FIELDS = [
        'title' => ['type' => 'string'],
        'description' => ['type' => 'string', 'optional' => TRUE],
        'category' => ['type' => 'integer']
    ];

    protected const IS_PICTURE_OPTIONAL = FALSE;

    private const EXTENSIONS = ['gif', 'jpg', 'jpeg', 'jpe', 'png', 'svg'];

    protected static function afterNormalizeData(&$data, &$errors)
    {
        $file = $_FILES['picture'];
        $error = $file['error'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $sizeNotAllowed = ($error == UPLOAD_ERR_INI_SIZE) || ($error == UPLOAD_ERR_FORM_SIZE);
        if ($error == UPLOAD_ERR_NO_FILE && !static::IS_PICTURE_OPTIONAL) {
            $errors['picture'] = 'Укажите файл с изображением';
        } elseif (!in_array($ext, self::EXTENSIONS)) {
            $errors['picture'] = 'Укажите файл с изображением ' .
                'в формате GIF, JPEG, PNG или SVG';
        } elseif ($sizeNotAllowed) {
            $errors['picture'] = 'Укажите файл размером не ' .
                'более 2 Мб';
        } elseif ($error != UPLOAD_ERR_OK) {
            $errors['picture'] = 'Файл не был отправлен';
        }
    }
}