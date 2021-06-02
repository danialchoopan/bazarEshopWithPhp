<?php
require_once '../../php/adminHeader.php';
$product_id = $_GET['Product_id'];
redirectIfUserNotLoggedAdmin();
$categories = readAllProductCategories();
$product = getProductById($product_id);
?>
<div class="w-100 p-3">
    <div class="card p-4 w-75 m-auto">
        <?php echo getMessageAlert() ?>
        <h4>بروزرسانی محصول</h4>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="idProduct" value="<?php echo $product['id'] ?>">
            <div class="mb-3">
                <lable>نام محصول</lable>
                <input type="text" name="ProductName" class="form-control" placeholder=""
                       value="<?php echo $product['name'] ?>"
                       required>
            </div>

            <div class="mb-3">
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <lable>عکس محصول</lable>
                            <input type="file" name="ProductPhoto" class="form-control">
                        </div>
                    </div>
                    <div class="col">
                        <img src="<?php echo APP_URL . 'img/' . $product['photo'] ?>" alt="" width="90%">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <lable>توضیحات محصول</lable>
                <textarea name="ProductDescription" class="form-control" rows="10"
                          required><?php echo $product['description'] ?></textarea>
            </div>
            <div class="mb-3">
                <lable>دسته بندی محصول</lable>
                <select name="ProductCategory" class="form-control">
                    <?php if ($product['category_product_id'] == 0) { ?>
                        <option value="0" selected>دسته بندی نشده</option>
                    <?php } else { ?>
                        <?php foreach ($categories as $category) { ?>
                            <?php if ($category['id'] == $product['category_product_id ']) { ?>
                                <option value="<?php echo $category['id'] ?>"
                                        selected><?php echo $category['name'] ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $category['id'] ?>"><?php echo $category['name'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>

            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="updateProduct"
                       value="بروزرسانی محصول"/>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
