<?php

namespace Controllers;

class APILogin extends BaseController
{
    public function check()
    {
        \Helpers\apiHeaders();
        $loginForm = \Forms\Login::getNormalizedData($_POST);
        if (isset($loginForm['__errors']) ||
            !\Forms\Login::verifyUser($loginForm)) {
            http_response_code(400);
            $user = ['errors' => $loginForm['__errors']];
        } else {
            $user = [
                'name' => $loginForm['name'],
                'token' => hash_hmac('ripemd256',
                $loginForm['name'], \Settings\SECRET_KEY)
            ];
        }
        echo json_encode($user, JSON_UNESCAPED_UNICODE);
    }
}