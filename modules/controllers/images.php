<?php

namespace Controllers;

class Images extends BaseController
{
    public function displayListImages()
    {
        $cats = new \Models\Category();
        $cats->select();
        $picts = new \Models\Picture();
        $picts->select('pictures.id, title, filename, uploaded, ' .
            'users.name AS user_name, categories.name AS cat_name, ' .
            'categories.slug, (SELECT COUNT(*) FROM comments ' .
            'WHERE comments.picture = pictures.id) AS comment_count',
            ['users', 'categories'],'', NULL, '', 0, \settings\COUNT_IMAGES_ON_PAGE);
        $ctx = ['cats' => $cats, 'picts' => $picts];
        $this->render('list', $ctx);
    }

    public function displayImage(int $index)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->currentUser) {
                throw new \Page403Exception();
            }
            \Helpers\checkToken($_POST);
            $commentForm = \Forms\Comment::getNormalizedData($_POST);
            if (!isset($commentForm['__errors'])) {
                $commentForm = \Forms\Comment::getPreparedData($commentForm);
                $commentForm['picture'] = $index;
                $commentForm['user'] = $this->currentUser['id'];
                $comments = new \Models\Comment();
                $comments->insert($commentForm);
                $pictures = new \Models\Picture();
                $author = $pictures->get($index, 'pictures.id',
                    'users.name, users.email, users.emailme',
                    ['users']);
                if ($author['emailme']) {
                    $values = [
                        'title' => \Settings\SITE_NAME,
                        'name' => $author['name'],
                        'url' => 'http://' . $_SERVER['SERVER_NAME'] .
                        '/' . $index
                    ];
                    \Helpers\sendMail($author['email'],
                        'notification_subject', 'notification_body',
                        $values);
                }
                $getParams = \Helpers\getGETParams(['page', 'filter', 'ref']);
                \Helpers\redirect('/' . $index . $getParams);
            }
        } else {
            $commentForm = \Forms\Comment::getInitialData();
        }

        $commentForm['__token'] = \Helpers\generateToken();
        $picts = new \Models\Picture();
        $pict = $picts->getOr404($index, 'pictures.id', 'pictures.id, title, ' .
            'description, filename, uploaded, users.name AS user_name, ' .
            'categories.name AS cat_name, categories.slug, ' .
            '(SELECT COUNT(*) FROM comments WHERE ' .
            'comments.picture = pictures.id) AS comment_count',
            ['users', 'categories']);
        $comments = new \Models\Comment();
        $comments->select('comments.id, contents, ' .
            'users.name AS user_name, uploaded, ' .
            'users.id AS user_id', ['users'], 'picture = ?', [$index]);
        $ctx = [
            'pict' => $pict,
            'site_title' => $pict['title'],
            'comments' => $comments,
            'form' => $commentForm
        ];
        $this->render('item', $ctx);
    }

    public function byCategory(string $slug)
    {
        $cats = new \Models\Category();
        $cat = $cats->getOr404($slug, 'slug', 'id, name');
        $whereStr = 'category = ?';
        $params = [$cat['id']];
        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            $whereStr .= ' AND (title LIKE ? OR description LIKE ?)';
            $param = '%' . $_GET['filter'] . '%';
            $params[] = $param;
            $params[] = $param;
        }
        $picts = new \Models\Picture();
        $pictCountRec = $picts->getRecord('COUNT(*) AS cnt', NULL, $whereStr, $params);
        $paginator = new \Paginator($pictCountRec['cnt'], ['filter']);
        $picts->select('pictures.id, title, filename, uploaded, ' .
            'users.name AS user_name, ' .
            '(SELECT COUNT(*) FROM comments WHERE ' .
            'comments.picture = pictures.id) AS comment_count',
            ['users'], $whereStr, $params, '',
            $paginator->firstRecordNum, \Settings\COUNT_IMAGES_ON_PAGE);
        $ctx = [
            'cat' => $cat, 
            'picts' => $picts,
            'paginator' => $paginator,
            'site_title' => $cat['name'] . ' :: Категории'
        ];
        $this->render('by_cat', $ctx);
    }

    public function byUser(string $userName)
    {
        $users = new \Models\User();
        $user = $users->getOr404($userName, 'name', 'id, name');
        $whereStr = 'user = ?';
        $params = [$user['id']];
        if (isset($_GET['filter']) && !empty($_GET['filter'])) {
            $whereStr .= ' AND (title LIKE ? OR description LIKE ?)';
            $param = '%' . $_GET['filter'] . '%';
            $params[] = $param;
            $params[] = $param;
        }
        $picts = new \Models\Picture();
        $pictCountRec = $picts->getRecord('COUNT(*) AS cnt', NULL,
            $whereStr, $params);
        $paginator = new \Paginator($pictCountRec['cnt'],
            ['filter']);
        $picts->select('pictures.id, title, filename, uploaded, ' .
            'categories.name AS cat_name, categories.slug, ' .
            '(SELECT COUNT(*) FROM comments WHERE ' .
            'comments.picture = pictures.id) AS comment_count, ' .
            'pictures.user', ['categories'], $whereStr, $params, '',
            $paginator->firstRecordNum, \Settings\COUNT_IMAGES_ON_PAGE);
        $ctx = [
            'user' => $user, 
            'picts' => $picts,
            'paginator' => $paginator,
            'site_title' => $user['name'] . ' :: Пользователи'
        ];
        $this->render('by_user', $ctx);
    }

    public function add(string $userName)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->currentUser) {
                throw new \Page403Exception();
            }
            \Helpers\checkToken($_POST);
            $pictureForm = \Forms\Picture::getNormalizedData($_POST);
            if (!isset($pictureForm['__errors'])) {
                $pictureForm = \Forms\Picture::getPreparedData($pictureForm);
                $users = new \Models\User();
                $user = $users->getOr404($userName, 'name', 'id');
                $pictureForm['user'] = $user['id'];
                $pictures = new \Models\Picture();
                $pictures->insert($pictureForm);
                \Helpers\redirect('/users/' . $userName);
            }
        } else {
            $pictureForm = \Forms\Picture::getInitialData();
        }

        $pictureForm['__token'] = \Helpers\generateToken();
        $categories = new \Models\Category();
        $categories->select();
        $ctx = [
            'site_title' => 'Добавление изображения',
            'username' => $userName,
            'form' => $pictureForm,
            'categories' => $categories
        ];
        $this->render('picture_add', $ctx);
    }

    private function checkUser(int $pictureIndex)
    {
        $pictures = new \Models\Picture();
        $picture = $pictures->getOr404($pictureIndex, 'id', 'user');
        $userExists = $this->currentUser;
        $userValid = $this->currentUser['id'] == $picture['user'] || $this->currentUser['admin'];
        if (!($userExists && $userValid)) {
            throw new \Page403Exception();
        }
    }

    public function edit(string $userName, int $pictureIndex)
    {
        $this->checkUser($pictureIndex);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $pictureForm = \Forms\Picture2::getNormalizedData($_POST);
            if (!isset($pictureForm['__errors'])) {
                $pictureForm = \Forms\Picture2::getPreparedData($pictureForm);
                $pictures = new \Models\Picture();
                $pictures->update($pictureForm, $pictureIndex);
                \Helpers\redirect('/users/' . $userName . \Helpers\getGETParams(['page', 'filter']));
            }
        } else {
            $pictures = new \Models\Picture();
            $picture = $pictures->getOr404($pictureIndex);
            $pictureForm = \Forms\Picture2::getInitialData($picture);
        }

        $pictureForm['__token'] = \Helpers\generateToken();
        $categories = new \Models\Category();
        $categories->select();
        $ctx = [
            'form' => $pictureForm,
            'categories' => $categories,
            'username' => $userName,
            'site_title' => 'Правка изображения'
        ];
        $this->render('picture_edit', $ctx);
    }

    public function delete(string $userName, int $pictureIndex)
    {
        $this->checkUser($pictureIndex);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            \Helpers\checkToken($_POST);
            $pictures = new \Models\Picture();
            $pictures->delete($pictureIndex);
            \Helpers\redirect('/users/' . $userName .
                \Helpers\getGETParams(['page', 'filter']));
        } else {
            $pictures = new \Models\Picture();
            $picture = $pictures->getOr404($pictureIndex, 'id', 'title, uploaded');
            $ctx = [
                'picture' => $picture,
                'username' => $userName,
                'site_title' => 'Удаление изображения',
                '__token' => \Helpers\generateToken()
            ];
            $this->render('picture_delete', $ctx);
        }
    }
}