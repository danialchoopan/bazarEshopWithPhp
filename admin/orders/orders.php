<?php
require_once '../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$orders = readAllBlogPosts();
?>
<div class="w-100 p-3">
    <h4 class="text-center p-2">تمامی سفاش ها</h4>
    <?php echo getMessageAlert() ?>
    <?php if (count($blogPosts)) { ?>
        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="thead-dark">
            <tr>
                <th scope="col">شمارش سفارش</th>
                <th scope="col">وضعیت سفارش</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($blogPosts as $blog) { ?>
                <tr>
                    <th scope="row"><?php echo $blog['id'] ?></th>
                    <td><?php echo $blog['title'] ?></td>
                    <?php if (getCategoryBlogById($blog['category_id'])) { ?>
                        <td><?php echo getCategoryBlogById($blog['category_id'])['name'] ?></td>
                    <?php } else { ?>
                        <td>دسته بندی نشده</td>
                    <?php } ?>
                    <td>
                        <img src="<?php echo APP_URL . 'img/' . $blog['photo'] ?>" width="100"></td>
                    <td>
                        <a href="?deleteBlogPostId=<?php echo $blog['id'] ?>" class="btn btn-danger">حذف</a>

                        <a href="<?php echo APP_URL . 'admin/post/update.php' ?>?BlogPostId=<?php echo $blog['id'] ?>"
                           class="btn btn-primary">نمایش/ویرایش</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="p-2 m-1">پستی جهت نمایش وجود ندارد</p>
    <?php } ?>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
