<?php
require_once 'php/header.php';
echo getMessageAlert()
?>
<div class="card p-2">
    <div class="row">
        <div class="col-4">
            <div class="row">
                <div class="col">
                    <img src="img/web/counter_strike_global_offensive_13-wallpaper-1280x720.jpg" height="100%"
                         width="100%" alt="">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <img src="img/web/counter_strike_global_offensive_13-wallpaper-1280x720.jpg" height="100%"
                         width="100%" alt="">
                </div>
            </div>
        </div>
        <div class="col-8">
            <img src="img/web/counter_strike_global_offensive_13-wallpaper-1280x720.jpg" height="100%" width="100%"
                 alt="">
        </div>
    </div>
    <div class="row">
        <div class="m-3 ">
            <h3>محصولات پرفروش</h3>
            <hr>
        </div>


        <div class="row">
            <?php
            $products = latestProducts();
            foreach ($products as $product) {
                ?>
                <a href="<?php echo APP_URL ?>product.php?product_id=<?php echo $product['id'] ?>"
                   class="text-decoration-none">
                    <div class="col-3 mb-2">
                        <div class="card h-100">
                            <img src="<?php echo APP_URL ?>img/<?php echo $product['photo'] ?>"
                                 class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title" style="color: black"><?php echo $product['name'] ?></h5>
                                <p class="card-text" style="color: black"><?php
                                    if (strlen($product['description']) > 100) {
                                        echo substr($product['description'], 100);
                                    } else {
                                        echo $product['description'];
                                    } ?> </p>
                                <p class="m-1" style="color: #008F00">
                                    <?php echo number_format($product['price']) . "تومان" ?>
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="?AddCartProduct_id=<?php echo $product['id'] ?>"
                                       class="btn btn-primary">افزودن به سبد خرید</a>
                                </div>
                            </div>
                            <div class="card-footer">
                                <small class="text-muted">
                                    <?php echo date('m/d/Y H:i:s', $product['created_at']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </a>
            <?php } ?>
        </div
        <?php
        getMessageAlert();
        ?>
    </div>
    <?php
    require_once 'php/footer.php'
    ?>
