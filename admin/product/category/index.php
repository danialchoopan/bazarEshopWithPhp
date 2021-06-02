<?php
require_once '../../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$categories = readAllProductCategories();
?>
<div class="w-100 p-3">
    <div class="row">
        <div class="col-3">

            <div class="card p-4 w-100">
                <h4>افزودن دسته بندی</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="text" name="ProductCategoryName" class="form-control" placeholder="نام دسته بندی"
                               required>
                    </div>
                    <p> نماد</p>
                    <div class="mb-3">
                        <input type="file" name="ProductCategoryPhoto" class="form-control" required>
                    </div>
                    <div class="d-grid gap-2">
                        <input type="submit" class="btn btn-primary" name="addProductCategory"
                               value="افزودن دسته بندی    "/>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-9">
            <h4 class="text-center p-2">دسته بندی ها</h4>
            <?php echo getMessageAlert() ?>
            <?php if (count($categories)) { ?>
                <table class="table table-hover table-bordered text-center align-middle">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">نام</th>
                        <th scope="col">نماد</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($categories as $category) { ?>
                        <tr>
                            <th scope="row"><?php echo $category['id'] ?></th>
                            <td><?php echo $category['name'] ?></td>
                            <td>
                                <img src="<?php echo APP_URL . 'img/' . $category['photo'] ?>" width="100"></td>
                            <td>
                                <a href="?deleteProductCategory=<?php echo $category['id'] ?>" class="btn btn-danger">حذف</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p class="p-2 m-1">دسته بندی جهت نمایش وجود ندارد</p>
            <?php } ?>
        </div>
    </div>
</div>
<?php
require_once '../../../php/adminFooter.php';
?>
