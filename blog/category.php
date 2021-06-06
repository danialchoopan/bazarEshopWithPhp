<?php
require_once '../php/header.php';
$category_id = $_GET['category_id'];
$category = getCategoryBlogById($category_id);
$blogPosts = getBlogPostsByCategoryId($category_id);
$blogCategories = readAllBlogCategories();
?>
<div class="card p-2">
    <div class="row">
        <div class="col-2 p-3">
            <p>دسته بندی وبلاگ</p>
            <ul class="list-group">
                <li class="list-group-item"><a
                            href="<?php echo APP_URL . 'blog/category.php?category_id=0' ?>"
                            class="text-decoration-none p-2">دسته بندی نشده</a>
                </li>
                <?php foreach ($blogCategories as $category) { ?>
                    <li class="list-group-item"><a
                                href="<?php echo APP_URL . 'blog/category.php?category_id=' . $category['id'] ?>"
                                class="text-decoration-none p-2"><?php echo $category['name'] ?></a>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-10 p-3">
            <?php if ($category_id == 0) { ?>
                <h4 class="mb-3">دسته بندی نشده</h4>
            <?php } else { ?>
                <h4 class="mb-3"><?php echo $category['name'] ?></h4>
            <?php } ?>
            <?php
            foreach ($blogPosts as $blog) {
                ?>
                <div class="card w-75 m-auto">
                    <img src="<?php echo APP_URL . 'img/' . $blog['photo'] ?>" class="card-img-top" alt="">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $blog['title'] ?></h5>
                        <p class="card-text">
                            <?php
                            if (strlen($blog['body']) > 200) {
                                echo substr($blog['body'], 200);
                            } else {
                                echo $blog['body'];
                            } ?>
                        </p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><?php echo getCategoryBlogById($blog['category_id'])['name'] ?></li>
                    </ul>
                    <div class="card-body">
                        <a href="<?php echo APP_URL . 'blog/blog.php?blog_id=' . $blog['id'] ?>" class="card-link">ادامه
                            مطلب</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php
require_once '../php/footer.php'
?>
