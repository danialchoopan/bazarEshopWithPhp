<?php
require_once 'php/header.php';
$userCarts = readUserCart();
$amout = 0;
?>
<div class="w-100 p-3">
    <h4 class="text-center p-2">سبد خرید</h4>
    <?php echo getMessageAlert() ?>
    <?php if ($userCarts) { ?>
        <table class="table table-hover table-bordered text-center align-middle table-dark">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">نام محصول</th>
                <th scope="col"></th>
                <th scope="col">قیمت</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody class="table">
            <?php foreach ($userCarts as $userCart) {
                $product = getProductById($userCart['product_id']);
                ?>
                <tr>
                    <th scope="row"><?php echo $userCart['id'] ?></th>
                    <td><?php echo $product['name'] ?></td>
                    <td><img src="<?php echo APP_URL ?>img/<?php echo $product['photo'] ?>" width="200" height="100"
                             alt=""></td>
                    <td style="color: #008F00"><?php echo number_format($product['price']) . ' تومان ';
                        $amout += $product['price'] ?></td>
                    <td>
                        <a href="?deleteProductFromCart=<?php echo $userCart['id'] ?>"
                           class="btn btn-outline-danger">حذف</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <div style="float: left">
            <p class="d-inline">
                مجموع سبد خرید
                :
            </p>
            <p class="m-1 d-inline"></p>
            <p style="color:#008F00 " class="d-inline"><?php echo number_format($amout) . ' تومان '; ?></p>
            <p class="m-1 d-inline"></p>
            <a href="" class="btn btn-success d-inline">ادامه فراید خرید</a>
            <p class="m-1 d-inline"></p>
        </div>
        <div class="clearfix"></div>
    <?php } else { ?>
        <div class="row">
            <div class="col">
                <div class="alert alert-warning " role="alert">سبد خرید شما خالی است</div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
require_once 'php/footer.php';
?>
