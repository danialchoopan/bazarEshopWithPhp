<?php
require_once '../php/header.php';
redirectIfUserNotLogged();
$user = checkIfUserLogin();
?>
<div class="w-100 p-4">
    <div class="card w-100 p-4">
        <h3>پروفایل کاربر</h3>
        <div class="row">
            <div class="col-8 p-2">
                <p>
                    نام کامل کاربر
                    :
                    <?php echo $user['name'] ?>
                </p>
                <p>
                    پست الکترونیک
                    :
                    <?php echo $user['email'] ?>
                </p>

                <p>
                    تلفن همراه
                    :
                    <?php echo $user['phone'] ?>
                </p>
                <p>
                    نقش کاربر
                    :
                    <?php if ($user['role']) {
                        echo "ادمین";
                    } else {
                        echo "کاربر";
                    } ?>
                </p>
            </div>
            <div class="col-4 p-2">
                <div class="d-grid gap-2">
                    <?php if ($user['role']) { ?>
                        <a class="btn btn-primary m-1" href="<?php echo APP_URL . 'admin/index.php' ?>">ورود به بخش
                            مدیریت</a>
                    <?php } ?>
                    <a class="btn btn-secondary m-1" href="<?php echo APP_URL . 'user/order/order.php' ?>">سفارش های من</a>
                    <a class="btn btn-danger m-1" href="<?php echo APP_URL . 'index.php?logoutUser=1' ?>">خروج از حساب
                        کاربری</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../php/footer.php';
?>
