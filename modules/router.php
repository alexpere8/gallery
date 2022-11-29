<?php

$requestPath = $_GET['route'];

if ($requestPath && $requestPath[-1] == '/') {
    $requestPath = substr($requestPath, 0, strlen($requestPath) - 1);
}

$availablePathsRegEx = [
    '/^cats\/(\w+)$/' => 'byCategory',
    '/^users\/(\w+)$/' => 'byUser',
    '/^(\d+)$/' => 'image',
    '/^users\/(\w+)\/pictures\/add$/' => 'addImage',
    '/^users\/(\w+)\/pictures\/(\d+)\/edit$/' => 'editImage',
    '/^users\/(\w+)\/pictures\/(\d+)\/delete$/' => 'deleteImage',
    '/^(\d+)\/comments\/(\d+)\/edit$/' => 'editComment',
    '/^(\d+)\/comments\/(\d+)\/delete$/' => 'deleteComment',
    '/^users\/(\w+)\/account\/delete$/' => 'deleteAccount',
    '/^users\/(\w+)\/account\/edit$/' => 'editeAccount',
    '/^users\/(\w+)\/account\/activation\/(\w+)$/' => 'activationAccount',
    '/^users\/(\w+)\/account\/editpassword$/' => 'editPassword',
    '/^api\/images\/(\d+)$/' => 'apiImages',
    '/^api\/images\/(\d+)\/comments$/' => 'apiComments',
    '/^api\/images\/(\d+)\/comments\/(\d+)$/' => 'apiComment',
];

$result = [];
$pathCode = $requestPath;

foreach($availablePathsRegEx as $pathRedEx => $path) {
    if (preg_match($pathRedEx, $requestPath, $result) === 1) {
        $pathCode = $path;
        break;
    }
}

switch ($pathCode) {
    case 'byCategory':
        $ctr = new \Controllers\Images();
        $ctr->byCategory($result[1]);
        break;
    case 'byUser':
        $ctr = new \Controllers\Images();
        $ctr->byUser($result[1]);
        break;
    case 'image':
        $index = (int)$result[1];
        $ctr = new \Controllers\Images();
        $ctr->displayImage($index);
        break;
    case 'addImage':
        $ctr = new \Controllers\Images();
        $ctr->add($result[1]);
        break;
    case 'editImage':
        $ctr = new \Controllers\Images();
        $ctr->edit($result[1], (int)$result[2]);
        break;
    case 'deleteImage':
        $ctr = new \Controllers\Images();
        $ctr->delete($result[1], (int)$result[2]);
        break;
    case 'editComment':
        $pictureIndex = (int)$result[1];
        $commentIndex = (int)$result[2];
        $ctr = new \Controllers\Comments();
        $ctr->edit($pictureIndex, $commentIndex);
        break;
    case 'deleteComment':
        $pictureIndex = (integer)$result[1];
        $commentIndex = (integer)$result[2];
        $ctr = new \Controllers\Comments();
        $ctr->delete($pictureIndex, $commentIndex);
        break;
    case 'deleteAccount':
        $ctr = new \Controllers\Account();
        $ctr->deleteAccount($result[1]);
        break;
    case 'editeAccount':
        $ctr = new \Controllers\Account();
        $ctr->editAccount($result[1]);
        break;
    case 'editPassword':
        $ctr = new \Controllers\Account();
        $ctr->editPassword($result[1]);
        break;
    case 'activationAccount':
        $ctr = new \Controllers\Account();
        $ctr->activate($result[1], $result[2]);
        break;
    case 'apiImages':
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $ctr = new \Controllers\APIImages();
            $ctr->item($result[1]);
        } else {
            http_response_code(405);
        }
        break;
    case 'apiComments':
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $ctr = new \Controllers\APIComments();
            $ctr->list($result[1]);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ctr = new \Controllers\APIComments();
            $ctr->add($result[1]);
        } else {
            http_response_code(405);
        }
        break;
    case 'apiComment':
        if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
            ($_POST['__method'] == 'PUT' ||
            $_POST['__method'] == 'PATCH')) {
            $ctr = new \Controllers\APIComments();
            $ctr->edit($result[1], $result[2]);
        } else if ($_SERVER['REQUEST_METHOD'] == 'POST' &&
            $_POST['__method'] == 'DELETE') {
            $ctr = new \Controllers\APIComments();
            $ctr->delete($result[1], $result[2]);
        } else {
            http_response_code(405);
        }
        break;
    case 'login':
        $ctr = new \Controllers\Login();
        $ctr->login();
        break;
    case 'logout':
        $ctr = new \Controllers\Login();
        $ctr->logout();
        break;
    case 'register':
        $ctr = new \Controllers\Login();
        $ctr->register();
        break;
    case 'register/complete':
        $ctr = new \Controllers\Login();
        $ctr->registerComplete();
        break;
    case 'api/login':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ctr = new \Controllers\APILogin();
            $ctr->check();
        } else {
            http_response_code(405);
        }
        break;
    case '':
        $ctr = new \Controllers\Images();
        $ctr->displayListImages();
        break;
    default:
        throw new Page404Exception();
}