<?php
require_once 'php/header.php';
$product_id = $_GET['product_id'];
$product = getProductById($product_id);
echo getMessageAlert()
?>
<div class="card p-3">
    <div class="row">
        <div class="col-7">
            <h4><?php echo $product['name'] ?></h4>
            <p class="m-1 p-1">
                <?php echo $product['description'] ?>
            </p>

            <hr>
            <p class="m-1" style="color: #008F00">
                <?php echo number_format($product['price']) . "تومان" ?>
            </p>
            <a href="?AddCartProduct_id=<?php echo $product['id'] ?>"
               class="btn btn-primary m-1">افزودن به سبد خرید</a>
        </div>

        <div class="col-5">
            <img src="<?php echo APP_URL ?>img/<?php echo $product['photo'] ?>" width="100%" alt="">
        </div>
    </div>
</div>
<?php
require_once 'php/footer.php'
?>
