<?php require \Helpers\getFragmentPath('__header') ?>
<div class="categories-wr">
    <h2>Категории</h2>
    <section id="categories">
        <?php foreach ($cats as $cat) { ?>
        <h3>
            <a href="/cats/<?php echo $cat['slug'] ?>/">
            <?php echo $cat['name'] ?>
            </a>
        </h3>
        <?php } ?>
    </section>
</div>
<h2>Изображения</h2>
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
                <a href="/users/<?php echo $pict['user_name'] ?>">
                <?php echo $pict['user_name'] ?>
                </a>
            </h4>
            <div class="item-date">
            <?php echo \Helpers\getFormattedTimestamp($pict['uploaded']) ?>
            </div>
            <?php
            $curUserHasPowers = ($__current_user && ($__current_user['id'] == $pict['user'] || $__current_user['admin']));
            if ($curUserHasPowers) { ?>
            <div class="action-btn"><a href="<?php echo '/users/', $pict['user_name'], '/pictures/',
                $pict['id'], '/edit', $gets ?>">Исправить</a>
            </div>
            <div class="action-btn"><a href="<?php echo '/users/', $pict['user_name'], '/pictures/',
                $pict['id'], '/delete', $gets ?>">Удалить</a>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
<?php require \Helpers\getFragmentPath('__footer') ?>