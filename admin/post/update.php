<?php
require_once '../../php/adminHeader.php';
$BlogPostId = $_GET['BlogPostId'];
redirectIfUserNotLoggedAdmin();
$categories = readAllBlogCategories();
$blog = getBlogPostsById($BlogPostId);
?>
<div class="w-100 p-3">
    <div class="card p-4 w-75 m-auto">
        <?php echo getMessageAlert() ?>
        <h4>بروزسانی پست بلاگ</h4>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="blogPostId" value="<?php echo $blog['id'] ?>">
            <div class="mb-3">
                <lable>عنوان پست</lable>
                <input type="text" name="BlogTitle" class="form-control" placeholder=""
                       value="<?php echo $blog['title'] ?>"
                       required>
            </div>

            <div class="mb-3">
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <lable>عکس پست</lable>
                            <input type="file" name="BlogPhoto" class="form-control"
                                   >
                        </div>

                    </div>
                    <div class="col">
                        <img src="<?php echo APP_URL . 'img/' . $blog['photo'] ?>" alt="" width="90%">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <lable>توضیحات پست</lable>
                <textarea name="BlogDescription" class="form-control" rows="10"
                          required><?php echo $blog['body'] ?></textarea>
            </div>
            <div class="mb-3">
                <lable>دسته بندی پست</lable>
                <select name="BlogCategory" class="form-control">
                    <?php if ($blog['category_id'] == 0) { ?>
                        <option value="0" selected>دسته بندی نشده</option>
                    <?php } else { ?>
                        <?php foreach ($categories as $category) { ?>
                            <?php if ($category['id'] == $blog['category_id']) { ?>
                                <option value="<?php echo $category['id'] ?>"
                                        selected><?php echo $category['name'] ?></option>
                            <?php } else { ?>
                                <option value="<?php echo $category['id'] ?>"><?php echo $category['name'] ?></option>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>

            <div class="d-grid gap-2">
                <input type="submit" class="btn btn-primary" name="updatePostBlog"
                       value="بروزرسانی پست"/>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../../php/adminFooter.php';
?>
