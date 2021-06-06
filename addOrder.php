<?php
require_once 'php/header.php';
$categories = readAllProductCategories();
?>
<div class="w-100 p-3">
    <div class="card p-4 w-75 m-auto">
        <?php echo getMessageAlert() ?>
        <h4>ادامه فرآیند خرید</h4>
        <div class="alert alert-info " role="alert">
            لطفا اطلاعات خواسته شده را پر کنید.
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <lable>شماره همراه</lable>
                <input type="text" name="orderPhone" class="form-control" placeholder=""
                       required>
            </div>
            <div class="mb-3">
                <lable>آدرس</lable>
                <textarea name="orderAddress" class="form-control" rows="10" required></textarea>
            </div>
            <div class="mb-3">
                <lable>توضیحات</lable>
                <textarea name="orderDescription" class="form-control" rows="10" required></textarea>
            </div>
            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="addOrderUser"
                       value="ثبت سفارش"/>
            </div>
        </form>
    </div>
</div>
<?php
require_once 'php/footer.php';
?>
