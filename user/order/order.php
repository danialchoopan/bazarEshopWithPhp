<?php
require_once '../../php/header.php';
$orders = readAllUserOrders();
?>
<div class="w-100 p-3">
    <h4 class="text-center p-2">سفارش های من</h4>
    <?php echo getMessageAlert() ?>
    <?php if ($orders) { ?>
        <table class="table table-hover table-bordered text-center align-middle table-dark">
            <thead>
            <tr>
                <th scope="col">شماره سفارش</th>
                <th scope="col">وضیعیت سفارش</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody class="table">
            <?php foreach ($orders as $order) {
                ?>
                <tr>
                    <td scope="row"  class="text-center"><?php echo $order['id'] ?></td>
                    <td  class="text-center"><?php
                        if ($order['status']) {
                            echo "<p class='text-success'>تایید شده</p>";
                        } else {
                            echo "<p class='text-danger'>تایید نشده</p>";
                        } ?></td>
                    <td class="text-center">
                        <a href="<?php echo APP_URL . 'user/order/product.php' ?>?order_id=<?php echo $order['id'] ?>"
                           class="btn btn-outline-primary">نمایش جزئیات سفارش</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <div class="row">
            <div class="col">
                <div class="alert alert-warning " role="alert">شما تابحال سفارشی ثبت نکرده اید</div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
require_once '../../php/footer.php';
?>
