<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">ویرایش نقش: <?= Sanitize::e($role['name']) ?></h4>
    <a href="/admin/roles" class="btn btn-outline-secondary btn-sm">بازگشت</a>
</div>

<div class="admin-form" style="max-width: 600px;">
    <form method="post" action="/admin/roles/<?= $role['id'] ?>/update">
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label class="form-label">نام نقش</label>
            <input type="text" class="form-control" name="name" required
                   value="<?= Sanitize::e($role['name']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">توضیحات</label>
            <input type="text" class="form-control" name="description"
                   value="<?= Sanitize::e($role['description'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">دسترسی‌ها</label>
            <?php foreach ($permissions as $perm): ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                           id="perm_<?= $perm['id'] ?>" <?= in_array($perm['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="perm_<?= $perm['id'] ?>">
                        <?= Sanitize::e($perm['description'] ?? $perm['name']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>بروزرسانی
            </button>
            <a href="/admin/roles" class="btn btn-outline-secondary">انصراف</a>
        </div>
    </form>
</div>
