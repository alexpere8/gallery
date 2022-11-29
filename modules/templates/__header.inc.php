<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="/styles.css" rel="stylesheet" type="text/css">
        <title>
            <?php echo ((isset($site_title)) ? $site_title . ' :: ' : '') ?>
            <?php echo \Settings\SITE_NAME ?>
        </title>
    </head>
    <body>
        <header>
            <div class="logo"><a href="/"><img src="/logo.png" alt=""></a></div>
            <h1><a href="/"><?php echo \Settings\SITE_NAME ?></a></h1>
        </header>
        <div class="content">
        <?php
            $user_name = $__current_user['name'];
            if ($__current_user['name1']) {
                $user_name = $__current_user['name1'];
            }

            if ($__current_user['name1'] && $__current_user['name2']) {
                $user_name = $__current_user['name1'] . ' ' . $__current_user['name2'];
            }
        ?>
        <section id="logged">
            <?php if ($__current_user) { ?>
                <a href="/users/<?php echo $__current_user['name'] ?>">
                <?php echo $user_name ?></a>
                <a href="/users/<?php echo $__current_user['name'] ?>/account/edit">Изменить данные</a>
                <a href="/users/<?php echo $__current_user['name'] ?>/account/editpassword">Сменить пароль</a>
                <a href="/logout">Выйти</a>
                <a href="/users/<?php echo $__current_user['name'] ?>/account/delete">Удалить пользователя</a>
            <?php } else { ?>
                <a href="/register">Зарегистрироваться</a>
                <a href="/login">Войти</a>
            <?php } ?>
        </section>