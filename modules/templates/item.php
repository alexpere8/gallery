<?php require \Helpers\getFragmentPath('__header') ?>
<h2><?php echo $pict['title'] ?></h2>
<?php
    switch ($_GET['ref'] ?? '') {
        case 'index':
            $ref = '/';
            break;
        case 'cat':
            $ref = '/cats/' . $pict['slug'] . '/';
            $ref .= \Helpers\getGETParams(['page', 'filter']);
            break;
        default:
            $ref = '/users/' . $pict['user_name'] . '/';
            $ref .= \Helpers\getGETParams(['page', 'filter']);
    }
?>
<div class="back-btn"><a href="<?php echo $ref ?>">Назад</a></div>
<div class="item-content">
    <section id="gallery-item">
        <img src="<?php echo \Settings\IMAGE_PATH . $pict['filename'] ?>">
    </section>
    <section class="item-description">
    <p><?php echo $pict['description'] ?></p>
    <H4>Категория: 
        <a href="/cats/<?php echo $pict['slug'] ?>/">
        <?php echo $pict['cat_name'] ?></a>
    </h4>
    <h4>Опубликовано пользователем:
        <a href="/users/<?php echo $pict['user_name'] ?>/">
        <?php echo $pict['user_name'] ?></a>
    </h4>
    <p>Дата и время публикации:
    <?php echo \Helpers\getFormattedTimestamp($pict['uploaded']) ?>
    </p>
    <?php if ($__current_user) { ?>
    <h3>Добавить комментарий</h3>
    <?php require \Helpers\getFragmentPath('__comment_form') ?>
    <?php } ?>
    <?php $urlPrt2 = \Helpers\getGETParams(['page', 'filter', 'ref']) ?>
    <?php if (is_countable($comments)) { ?>
    <h3>Комментарии</h3>
    <?php } ?>
    <?php foreach ($comments as $comment) { ?>
        <h5><?php echo $comment['user_name'] ?></h5>
        <p><?php echo $comment['contents'] ?></p>
        <p>Опубликован:
        <?php echo \Helpers\getFormattedTimestamp($comment['uploaded']) ?>
        </p>
        <?php 
        $curUserHasPowers = $__current_user && ($__current_user['id'] == $comment['user_id'] || $__current_user['admin']);
        if ($curUserHasPowers) { ?>
        <?php $urlPrt1 = '/' . $pict['id'] . '/comments/' . $comment['id'] ?>
        <p><a href="<?php echo $urlPrt1 . '/edit' . $urlPrt2 ?>">Исправить</a>
        <a href="<?php echo $urlPrt1 . '/delete' . $urlPrt2 ?>">Удалить</a></p>
        <?php } ?>
        <p>&nbsp;</p>
    <?php } ?>
    </section>
</div>
<div class="back-btn"><a href="<?php echo $ref ?>">Назад</a></div>
<?php require \Helpers\getFragmentPath('__footer') ?>