<?php
require_once '../php/header.php';
redirectIfUserLogged();
?>
<div class="w-100 p-4">
    <div class="card p-4 w-75 m-auto">
        <h4>ورود به حساب کاربری</h4>
        <?php echo getMessageAlert() ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">پست الکترونیک</label>
                <input type="email" name="loginUser_email" class="form-control" aria-describedby="emailHelp">
            </div>
            <div class="mb-3">
                <label class="form-label">رمزعبور</label>
                <input type="password" name="loginUser_password" class="form-control">
            </div>
            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="loginUser" value="ورود">
            </div>
        </form>
    </div>
</div>
<?php
require_once '../php/footer.php';
?>
