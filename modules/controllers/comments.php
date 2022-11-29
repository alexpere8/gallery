<?php

namespace Controllers;

class Comments extends BaseController
{
    private function checkUser(int $commentIndex)
    {
        $comments = new \Models\Comment();
        $comment = $comments->getOr404($commentIndex, 'id', 'user');
        $userExists = $this->currentUser;
        $userValid = $this->currentUser['id'] == $comment['user'] || $this->currentUser['admin'];
        if (!($userExists && $userValid)) {
            throw new \Page403Exception();
        }
    }

    public function edit(int $pictureIndex, int $commentIndex)
    {
        $this->checkUser($commentIndex);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $commentForm = \Forms\Comment::getNormalizedData($_POST);
            if (!isset($commentForm['__errors'])) {
                $commentForm = \Forms\Comment::getPreparedData($commentForm);
                $comments = new \Models\Comment();
                $comments->update($commentForm, $commentIndex);
                \Helpers\redirect('/' . $pictureIndex .
                    \Helpers\getGETParams(['page', 'filter',
                    'ref']));
            }
        } else {
            $comments = new \Models\Comment();
            $comment = $comments->getOr404($commentIndex);
            $commentForm = \Forms\Comment::getInitialData($comment);
        }

        $commentForm['__token'] = \Helpers\generateToken();
        $users = new \Models\User();
        $users->select('*', NULL, '', NULL, 'name');
        $ctx = [
            'form' => $commentForm, 
            'users' => $users,
            'picture' => $pictureIndex,
            'site_title' => 'Правка комментария'
        ];
        $this->render('comment_edit', $ctx);
    }

    public function delete(int $pictureIndex, int $commentIndex)
    {
        $this->checkUser($commentIndex);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $comments = new \Models\Comment();
            $comments->delete($commentIndex);
            $getParams = \Helpers\getGETParams(['page', 'filter', 'ref']);
            \Helpers\redirect('/' . $pictureIndex . $getParams);
        } else {
            $comments = new \Models\Comment();
            $comment = $comments->getOr404($commentIndex,
                'comments.id',
                'users.name AS user_name, contents, uploaded',
                ['users']);
            $ctx = [
                'comment' => $comment,
                'picture' => $pictureIndex,
                'site_title' => 'Удаление комментария',
                '__token' => \Helpers\generateToken()
            ];
            $this->render('comment_delete', $ctx);
        }
    }
}