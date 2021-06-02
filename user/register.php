<?php
require_once '../php/header.php';
redirectIfUserLogged();
?>
<div class="w-100 p-4">
    <div class="card p-4 w-75 m-auto">
        <h4>نام نویسی</h4>
        <?php echo getMessageAlert() ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">نام</label>
                <input type="text" name="registerUser_name" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">نام خانوادگی</label>
                <input type="text" name="registerUser_last_name" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">شماره همراه</label>
                <input type="text" name="registerUser_phone" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">پست الکترونیک</label>
                <input type="email" name="registerUser_email" class="form-control" aria-describedby="emailHelp">
                <div class="form-text">لطفا پست الکترونیک معتبر انتخاب کنید.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">رمزعبور</label>
                <input type="password" name="registerUser_password" class="form-control">
            </div>
            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="registerUser" value="نام نویسی"/>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../php/footer.php';
?>
