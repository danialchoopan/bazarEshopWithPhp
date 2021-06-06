<?php
require_once '../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$orders = readAllOrdersByStatus(1);
?>
<div class="w-100 p-3">
    <h4 class="text-center p-2">تمامی سفاش ها تایید شده</h4>
    <?php echo getMessageAlert() ?>
    <?php if (count($orders)) { ?>
        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="thead-dark">
            <tr>
                <th scope="col">شمارش سفارش</th>
                <th scope="col">وضعیت سفارش</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order) { ?>
                <tr>
                    <th scope="row" class="text-center"><?php echo $order['id'] ?></th>
                    <td class="text-center"><?php
                        if ($order['status']) {
                            echo "<p class='text-success'>تایید شده</p>";
                        } else {
                            echo "<p class='text-danger'>تایید نشده</p>";
                        } ?>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo APP_URL . 'admin/order/product_order.php' ?>?order_id=<?php echo $order['id'] ?>"
                           class="btn btn-outline-primary">نمایش جزئیات سفارش</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="p-2 m-1">سفارشی جهت نمایش وجود ندارد</p>
    <?php } ?>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
