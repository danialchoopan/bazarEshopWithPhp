<?php
require_once '../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$user = checkIfUserLogin();
?>
<div class="w-100 p-4">
    <div class="card w-100 p-4">
        <h3>اطلاعات ادمین</h3>
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
                    <p>
                        در این قسمت می توانید محصولات ، پست های وبلاگ ، سفارشات را مدیریت کنید
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../php/adminFooter.php';
?>
