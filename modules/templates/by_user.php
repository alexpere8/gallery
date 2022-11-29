<?php require \Helpers\getFragmentPath('__header') ?>
<?php require \Helpers\getFragmentPath('__filter_form') ?>
<?php $gets = \Helpers\getGETParams(['page', 'filter']) ?>
<h2><?php echo $user['name'] ?></h2>
<?php if ($__current_user && $__current_user['id'] == $user['id']) { ?>
<div class="action-btn"><a href="<?php echo '/users/' . $user['name'] .
    '/pictures/add'. $gets ?>">Добавить изображение</a>
</div>
<?php } ?>
<div id="gallery">
    <?php foreach ($picts as $pict) { ?>
    <div class="item">
        <div class="item-title">
            <a class="item-link" href="/<?php echo $pict['id'] ?>/?ref=index">
                <h3><?php echo $pict['title'] ?></h3>
            </a>
        </div>
        <a href="/<?php echo $pict['id'] ?>/?ref=index">
            <img src="<?php echo \Helpers\getThumbnail($pict['filename']) ?>">
         </a>
        <div class="item-description">
            <h4>
            <a href="/cats/<?php echo $pict['slug'] ?>">
                <?php echo $pict['cat_name'] ?>
            </a>
            </h4>
            <h4>
                <a href="/users/<?php echo $user['name'] ?>">
                <?php echo $user['name'] ?>
                </a>
            </h4>
            <div class="item-date">
            <?php echo \Helpers\getFormattedTimestamp($pict['uploaded']) ?>
            </div>
            <?php
            $curUserHasPowers = ($__current_user && ($__current_user['id'] == $pict['user'] || $__current_user['admin']));
            if ($curUserHasPowers) { ?>
            <div class="action-btn"><a href="<?php echo '/users/', $user['name'], '/pictures/',
                $pict['id'], '/edit', $gets ?>">Исправить</a>
            </div>
            <div class="action-btn"><a href="<?php echo '/users/', $user['name'], '/pictures/',
                $pict['id'], '/delete', $gets ?>">Удалить</a>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
<?php require \Helpers\getFragmentPath('__paginator') ?>
<?php require \Helpers\getFragmentPath('__footer') ?>