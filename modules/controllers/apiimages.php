<?php

namespace Controllers;

class APIImages extends BaseController
{
    public function item(int $index)
    {
        \Helpers\apiHeaders();
        $picts = new \Models\Picture();
        $pict = $picts->getOr404($index, 'id',
            'title, description, filename');
        $pict['url'] = 'http://' . $_SERVER['SERVER_NAME'] .
            \Settings\IMAGE_PATH . $pict['filename'];
        echo json_encode($pict, JSON_UNESCAPED_UNICODE);
    }
}