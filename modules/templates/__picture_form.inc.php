<form class="bigform" method="post" enctype="multipart/form-data">
    <input type="hidden" name="__token" value="<?php echo $form['__token'] ?>">
    <label for="picture_category">Категория</label>
    <select id="picture_category" name="category">
        <?php foreach ($categories as $category) { ?>
            <option value="<?php echo $category['id'] ?>"
            <?php echo ($form['category'] == $category['id']) ? 'selected' : ''?>>
                <?php echo $category['name'] ?>
            </option>
        <?php } ?>
    </select>
    <label for="picture_title">Название</label>
    <input type="text" id="picture_title" name="title" value="<?php echo $form['title'] ?>">
    <?php \Helpers\showErrors('title', $form) ?>
    <label for="picture_description">Описание</label>
    <textarea id="picture_description" name="description"><?php echo $form['description'] ?></textarea>
    <label for="picture_file">Файл с изображением</label>
    <input type="file" id="picture_file" name="picture">
    <?php \Helpers\showErrors('picture', $form) ?>
    <input type="submit" value="Отправить">
</form>