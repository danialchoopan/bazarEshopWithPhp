<?php
require_once '../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$categories = readAllBlogCategories();
?>
<div class="w-100 p-3">
    <div class="card p-4 w-75 m-auto">
        <?php echo getMessageAlert() ?>
        <h4>افزودن پست بلاگ</h4>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <lable>عنوان پست</lable>
                <input type="text" name="BlogTitle" class="form-control" placeholder=""
                       required>
            </div>
            <div class="mb-3">
                <lable>عکس پست</lable>
                <input type="file" name="BlogPhoto" class="form-control"
                       required>
            </div>
            <div class="mb-3">
                <lable>توضیحات پست</lable>
                <textarea name="BlogDescription" class="form-control" rows="10" required></textarea>
            </div>
            <div class="mb-3">
                <lable>دسته بندی پست</lable>
                <select name="BlogCategory" class="form-control">
                    <option value="0">دسته بندی نشده</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['id'] ?>"><?php echo $category['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="addPostBlog"
                       value="افزودن پست"/>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
