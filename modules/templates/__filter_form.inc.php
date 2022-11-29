<?php
    if (!empty($_GET['filter'])) {
        $filter = $_GET['filter'];
    } else {
        $filter = '';
    }
?>
<form id="filter_form" method="get">
    <input type="text" name="filter" placeholder="Фильтрация"
    value="<?php echo $filter ?>">
    <input type="submit" value="Вперед">
</form>