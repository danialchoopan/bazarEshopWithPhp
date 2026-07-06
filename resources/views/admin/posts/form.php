<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<h4 class="fw-bold mb-4">
    <?= $post ? 'ویرایش پست' : 'افزودن پست جدید' ?>
</h4>

<div class="admin-form" style="max-width: 800px;">
    <form method="post" action="<?= $post ? '/admin/posts/' . $post['id'] . '/update' : '/admin/posts' ?>" enctype="multipart/form-data">
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label class="form-label">عنوان <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" required
                   value="<?= Sanitize::e($post['title'] ?? $_POST['title'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">دسته‌بندی</label>
            <select class="form-select" name="category_id">
                <option value="0">بدون دسته‌بندی</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                        <?= Sanitize::e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">تصویر</label>
            <input type="file" class="form-control" name="photo" accept="image/*">
            <?php if (!empty($post['photo']) && str_starts_with($post['photo'], 'uploads/')): ?>
                <div class="mt-2">
                    <img src="/<?= htmlspecialchars($post['photo']) ?>" width="100" height="100" style="object-fit: cover; border-radius: 8px;" alt="">
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">محتوا</label>
            <textarea class="form-control" name="body" id="postBody" rows="12"><?= $post['body'] ?? $_POST['body'] ?? '' ?></textarea>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i><?= $post ? 'بروزرسانی' : 'ذخیره' ?>
            </button>
            <a href="/admin/posts" class="btn btn-outline-secondary">انصراف</a>
        </div>
    </form>
</div>

<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor.create(document.querySelector('#postBody'), {
    direction: 'rtl',
    language: 'fa'
}).catch(error => {
    console.error(error);
});
</script>
