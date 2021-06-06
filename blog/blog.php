<?php
require_once '../php/header.php';
$blog_id = $_GET['blog_id'];
$blog = getBlogPostsById($blog_id);
?>
<div class="card p-2">
    <div class="row">
        <div class="col-10 p-3 m-auto">
            <div class="card w-75 m-auto">
                <img src="<?php echo APP_URL . 'img/' . $blog['photo'] ?>" class="card-img-top" alt="">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $blog['title'] ?></h5>
                    <p class="card-text">
                        <?php
                        echo $blog['body'];
                        ?>
                    </p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><?php echo getCategoryBlogById($blog['category_id'])['name'] ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
require_once '../php/footer.php'
?>
