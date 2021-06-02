<?php
require_once '../../php/adminHeader.php';
redirectIfUserNotLoggedAdmin();
$users = readAllUsers();
?>
<div class="w-100 p-3">
    <h4 class="text-center p-2">کاربران</h4>
    <?php echo getMessageAlert() ?>
    <?php if (count($users)) { ?>
        <table class="table table-hover table-bordered text-center align-middle table-dark">
            <thead >
            <tr>
                <th scope="col">#</th>
                <th scope="col">نام</th>
                <th scope="col">نام خانوادگی</th>
                <th scope="col">پست الکترونیک</th>
                <th scope="col">تلفن همراه</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody class="table">
            <?php foreach ($users as $user) { ?>
                <tr>
                    <th scope="row"><?php echo $user['id'] ?></th>
                    <td><?php echo $user['name'] ?></td>
                    <td><?php echo $user['last_name'] ?></td>
                    <td><?php echo $user['email'] ?></td>
                    <td><?php echo $user['phone'] ?></td>
                    <td>
                        <a href="<?php echo APP_URL . 'admin/user/orders.php' ?>?user_id=<?php echo $user['id'] ?>"
                           class="btn btn-outline-primary">نمایش سابفه سفارشات این کاربر</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="p-2 m-1">کاربری جهت نمایش وجود ندارد</p>
    <?php } ?>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
