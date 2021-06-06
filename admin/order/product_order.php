<?php
require_once '../../php/adminHeader.php';
$order_id = $_GET['order_id'];
$order = getUserOrderById($order_id);
?>
<div class="w-100 p-3">
    <div class="card p-3">
        <h4 class="text-center p-2">جزئیات سفارش</h4>
        <p class="m-1">
        <p>
            <span>شماره همراه : </span>
            <?php echo $order['phone'] ?>
        </p>
        <p>
            <span>آدرس : </span>
            <?php echo $order['user_address'] ?>
        </p>
        <p>
            <span>توضیحات : </span>
            <?php echo $order['description'] ?>
        </p>
        <p>
            <span>وضعیت : </span>
            <?php if ($order['status']) {
                echo "<span class='text-success'>تایید شده</span>";
            } else {
                echo "<span class='text-danger'>تایید نشده</span>";
            } ?>
        </p>
        </p>
    </div>
    <?php echo getMessageAlert() ?>
    <table class="table table-hover table-bordered text-center align-middle table-dark">
        <thead>
        <tr>
            <th scope="col">نام محصول</th>
            <th scope="col"></th>
            <th scope="col">قیمت</th>
        </tr>
        </thead>
        <tbody class="table">
        <?php
        $id_products = unserialize($order['products']);
        foreach ($id_products as $idProduct) {
            $product = getProductById($idProduct);
            ?>
            <tr>
                <td><?php echo $product['name'] ?></td>
                <td><img src="<?php echo APP_URL ?>img/<?php echo $product['photo'] ?>" width="200" height="100"
                         alt=""></td>
                <td style="color: #008F00"><?php echo number_format($product['price']) . ' تومان ' ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
