<?php
require_once '../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$products = readAllProduct();
?>
<div class="w-100 p-3">
    <h4 class="text-center p-2">محصولات</h4>
    <?php echo getMessageAlert() ?>
    <?php if (count($products)) { ?>
        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">نام</th>
                <th scope="col">قیمت</th>
                <th scope="col">درسته بندی</th>
                <th scope="col">نماد</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <th scope="row"><?php echo $product['id'] ?></th>
                    <td><?php echo $product['name'] ?></td>
                    <td><?php echo $product['price'] ?></td>
                    <td><?php echo getCategoryById($product['category_product_id'])['name'] ?></td>
                    <td>
                        <img src="<?php echo APP_URL . 'img/' . $product['photo'] ?>" width="100"></td>
                    <td>
                        <a href="?deleteProduct=<?php echo $product['id'] ?>" class="btn btn-danger">حذف</a>

                        <a href="<?php echo APP_URL . 'admin/product/updateProduct.php' ?>?Product_id=<?php echo $product['id'] ?>"
                           class="btn btn-primary">نمایش/ویرایش</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="p-2 m-1">محصول جهت نمایش وجود ندارد</p>
    <?php } ?>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
