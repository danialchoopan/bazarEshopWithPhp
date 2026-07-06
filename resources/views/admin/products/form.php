<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<h4 class="fw-bold mb-4">
    <?= $product ? 'ویرایش محصول' : 'افزودن محصول جدید' ?>
</h4>

<div class="admin-form" style="max-width: 700px;">
    <form method="post" action="<?= $product ? '/admin/products/' . $product['id'] . '/update' : '/admin/products' ?>" enctype="multipart/form-data">
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label class="form-label">نام محصول <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" required
                   value="<?= Sanitize::e($product['name'] ?? $_POST['name'] ?? '') ?>">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">قیمت (تومان) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="price" required min="0"
                       value="<?= $product['price'] ?? $_POST['price'] ?? '' ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">موجودی</label>
                <input type="number" class="form-control" name="stock" min="0"
                       value="<?= $product['stock'] ?? $_POST['stock'] ?? 0 ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">دسته‌بندی</label>
            <select class="form-select" name="category_id">
                <option value="0">بدون دسته‌بندی</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($product['category_product_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                        <?= Sanitize::e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">تصویر</label>
            <input type="file" class="form-control" name="photo" accept="image/*">
            <?php if (!empty($product['photo'])): ?>
                <div class="mt-2">
                    <?php
                    $photoUrl = str_starts_with($product['photo'], 'uploads/')
                        ? '/' . $product['photo']
                        : '/img/' . $product['photo'];
                    ?>
                    <img src="<?= htmlspecialchars($photoUrl) ?>" width="100" height="100" style="object-fit: cover; border-radius: 8px;" alt="">
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">توضیحات</label>
            <textarea class="form-control" name="description" rows="4"><?= Sanitize::e($product['description'] ?? $_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" name="is_active" id="is_active"
                   <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">محصول فعال باشد</label>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i><?= $product ? 'بروزرسانی' : 'ذخیره' ?>
            </button>
            <a href="/admin/products" class="btn btn-outline-secondary">انصراف</a>
        </div>
    </form>
</div>
