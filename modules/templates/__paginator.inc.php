<?php if ($paginator->pageCount > 1) { ?>
<div id="paginator">
    <?php foreach ($paginator as $number => $url) {
        if ($paginator->currentPage == $number) { ?>
            <span><?php echo $number ?></span>
        <?php } else { ?>
            <a href="<?php echo $url ?>"><?php echo $number ?></a>
        <?php } ?>
    <?php } ?>
</div>
<?php } ?>