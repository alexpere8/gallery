<?php

namespace Controllers;

class APIComments extends BaseController
{
    public function list(int $pictureIndex)
    {
        \Helpers\apiHeaders();
        $comments = new \Models\Comment();
        $coms = $comments->getAll('comments.id, contents, ' .
            'users.name AS user_name', ['users'], 'picture = ?',
            [$pictureIndex]);
        echo json_encode($coms, JSON_UNESCAPED_UNICODE);
    }

    private function getUser()
    {
        $userName = $_POST['name'];
        if (hash_hmac('ripemd256', $userName,
            \Settings\SECRET_KEY) != $_POST['token']) {
            return FALSE;
        } else {
            $users = new \Models\User();
            return $users->get($userName, 'name');
        }
    }

    public function add(int $pictureIndex)
    {
        \Helpers\apiHeaders();
        if (!($user = $this->getUser())) {
            http_response_code(403);
        } else {
            $commentForm =
                \Forms\Comment::getNormalizedData($_POST);
            if (isset($commentForm['__errors'])) {
                http_response_code(400);
                $comment = ['errors' => $commentForm['__errors']];
                echo json_encode($comment, JSON_UNESCAPED_UNICODE);
            } else {
                $commentForm['picture'] = $pictureIndex;
                $commentForm['user'] = $user['id'];
                $comments = new \Models\Comment();
                $comments->insert($commentForm);
                http_response_code(201);
            }
        }
    }

    public function edit(int $pictureIndex, int $commentIndex)
    {
        \Helpers\apiHeaders();
        if (!($user = $this->getUser())) {
            http_response_code(403);
        } else {
            $comments = new \Models\Comment();
            $comment = $comments->get($commentIndex);
            if (!$comment) {
                http_response_code(404);
            }
            else if ($comment['user'] != $user['id']) {
                http_response_code(403);
            }
            else {
                $commentForm =
                    \Forms\Comment::getNormalizedData($_POST);
                if (isset($commentForm['__errors'])) {
                    http_response_code(400);
                    $comment = ['errors' => $commentForm['__errors']];
                    echo json_encode($comment, JSON_UNESCAPED_UNICODE);
                } else {
                    $comments = new \Models\Comment();
                    $comments->update($commentForm, $commentIndex);
                    http_response_code(200);
                }
            }
        }
    }

    public function delete(int $pictureIndex, int $commentIndex)
    {
        \Helpers\apiHeaders();
        if (!($user = $this->getUser())) {
            http_response_code(403);
        } else {
            $comments = new \Models\Comment();
            $comment = $comments->get($commentIndex);
            if (!$comment) {
                http_response_code(404);
            } else if ($comment['user'] != $user['id']) {
                http_response_code(403);
            } else {
                $comments = new \Models\Comment();
                $comments->delete($commentIndex);
                http_response_code(204);
            }
        }
    }
}