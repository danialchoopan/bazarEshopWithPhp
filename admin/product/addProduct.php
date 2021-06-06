<?php
require_once '../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$categories = readAllProductCategories();
?>
<div class="w-100 p-3">
    <div class="card p-4 w-75 m-auto">
        <?php echo getMessageAlert() ?>
        <h4>افزودن محصول</h4>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <lable>نام محصول</lable>
                <input type="text" name="ProductName" class="form-control" placeholder=""
                       required>
            </div>
            <div class="mb-3">
                <lable>قیمت محصول</lable>
                <input type="text" name="ProductPrice" class="form-control" placeholder=""
                       required>
            </div>
            <div class="mb-3">
                <lable>عکس محصول</lable>
                <input type="file" name="ProductPhoto" class="form-control"
                       required>
            </div>
            <div class="mb-3">
                <lable>توضیحات محصول</lable>
                <textarea name="ProductDescription" class="form-control" rows="10" required></textarea>
            </div>
            <div class="mb-3">
                <lable>دسته بندی محصول</lable>
                <select name="ProductCategory" class="form-control">
                    <option value="0">دسته بندی نشده</option>
                    <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category['id'] ?>"><?php echo $category['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="addProduct"
                       value="افزودن محصول"/>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
