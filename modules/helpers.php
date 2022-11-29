<?php

namespace Helpers;

function render(string $template, array $context)
{
    global $basePath;
    extract($context);
    require_once $basePath . '\modules\templates\\' . $template . '.php';
}

function getFragmentPath(string $fragment): string
{
    global $basePath;
    return $basePath . '\modules\templates\\' . $fragment . '.inc.php';
}

function connectToDB()
{
    $connStr = 'mysql:host=' . \Settings\DB_HOST . ';dbname=' .
        \Settings\DB_NAME . ';charset=utf8';
    return new \PDO($connStr, \Settings\DB_USERNAME, \Settings\DB_PASSWORD);
}

function getGETParams(array $existingParamNames, array $newParams = []): string
{
    $paramsArray = [];
    foreach ($existingParamNames as $name) {
        if (!empty($_GET[$name])) {
            $paramsArray[] = $name . '=' . urlencode($_GET[$name]);
        }
    }
    foreach ($newParams as $name => $value) {
        $paramsArray[] = $name . '=' . urlencode($value);
    }
    $paramsStr = implode('&', $paramsArray);
    if ($paramsStr) {
        $paramsStr = '?' . $paramsStr;
    }
    return $paramsStr;
}

function getFormattedTimestamp(string $timestamp): string
{
    $timestamp = strtotime($timestamp);
    $res = date('d.m.Y H:i', $timestamp);
    return $res;
}

function redirect(string $url, int $status = 302)
{
    header('Location: ' . $url, TRUE, $status);
}

function showErrors(string $fldName, array $formData)
{
    if (isset($formData['__errors'][$fldName])) {
        echo '<div class="error">' . $formData['__errors'][$fldName] . '</div>';
    }
}

function getFileName(array $file): string
{
    global $basePath;
    $imageFilePath = $basePath . \Settings\IMAGE_FILE_PATH;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $name = date('YmdHM');
    $postfix = '';
    $number = 0;
    $fullFileName = $imageFilePath . $name . $postfix . '.' . $ext;
    while (file_exists($fullFileName)) {
        $postfix = '_' . ++$number;
        $fullFileName = $imageFilePath . $name . $postfix . '.' . $ext;
    }
    $fileName = $name . $postfix . '.' . $ext;
    return $fileName;
}

function saveFile(array $file): string
{
    global $basePath;
    $imageFilePath = $basePath . \Settings\IMAGE_FILE_PATH;
    $fileName = getFileName($file);
    move_uploaded_file($file['tmp_name'], $imageFilePath . $fileName);
    return $fileName;
}

function deleteFile(string $fileName)
{
    global $basePath;
    $filePath = $basePath . \Settings\IMAGE_FILE_PATH . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

function getThumbnail(string $fileName)
{
    global $basePath;
    $thumbFileName = pathinfo($fileName, PATHINFO_FILENAME) . '.jpg';
    $thumbPath = $basePath . \Settings\THUMBNAIL_FILE_PATH . $thumbFileName;
    if (!file_exists($thumbPath)) {
        $filePath = $basePath . \Settings\IMAGE_FILE_PATH . $fileName;
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        switch ($fileExt) {
            case 'gif':
                $srcImg = imagecreatefromgif($filePath);
                break;
            case 'jpg':
            case 'jpeg':
            case 'jpe':
                $srcImg = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $srcImg = imagecreatefrompng($filePath);
        }
        $img = imagescale($srcImg, 450);
        $stwhite = imagecolorallocatealpha($img, 255, 255, 255, 32);
        $height = imagesy($img);
        imagejpeg($img, $thumbPath);
        imagedestroy($img);
        imagedestroy($srcImg);
    }
    return \Settings\THUMBNAIL_PATH . $thumbFileName;
}

function deleteThumbnail(string $fileName)
{
    global $basePath;
    $thumbPath = $basePath . \Settings\THUMBNAIL_FILE_PATH .
        pathinfo($fileName, PATHINFO_FILENAME) . '.jpg';
    if (file_exists($thumbPath)) {
        unlink($thumbPath);
    }
}

function renderText(string $template, array $values): string {
    $literals = [];
    $vals = [];
    foreach ($values as $key => $value) {
        $literals[] = '/%' . $key . '%/iu';
        $vals[] = $value;
    }
    return preg_replace($literals, $vals, $template);
}

function sendMail(string $to, string $subject, string $body, array $values)
{
    global $basePath;
    require_once $basePath . 'modules\SendMailSmtpClass.php';
    if (\Settings\MAIL_SEND) {
        $letter = new \SendMailSmtpClass(\Settings\SMTP_LOGIN,
            \Settings\SMTP_PASSWORD, \Settings\SMTP_HOST,
            \Settings\SMTP_PORT);
        $subjectDraft = $basePath . '\modules\templates\\' . $subject .
            '.txt';
        $subject = renderText(file_get_contents($subjectDraft), $values);
        $bodyDraft = $basePath . '\modules\templates\\' . $body . '.txt';
        $body = renderText(file_get_contents($bodyDraft), $values);
        $letter->send($to, $subject, $body, \Settings\MAIL_FROM);
    }
}

function generateToken(): string
{
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    $token = bin2hex(random_bytes(32));
    $_SESSION[$token] = 'anti_csrf';
    return $token;
}

function checkToken(array $form_data)
{
    if (empty($form_data['__token'])) {
        throw new \Page403Exception();
    }
    $token = $form_data['__token'];
    if (empty($_SESSION[$token])) {
        throw new \Page403Exception();
    }
    $val = $_SESSION[$token];
    unset($_SESSION[$token]);
    if ($val != 'anti_csrf') {
        throw new \Page403Exception();
    }
}

function apiHeaders()
{
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-cache, no-store, must-revalidate');
}