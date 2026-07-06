<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">مدیریت نقش‌ها</h4>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="admin-form">
            <h6 class="fw-bold mb-3">افزودن نقش جدید</h6>
            <form method="post" action="/admin/roles">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label class="form-label">نام نقش</label>
                    <input type="text" class="form-control" name="name" required
                           placeholder="مثلا: manager">
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <input type="text" class="form-control" name="description">
                </div>

                <div class="mb-3">
                    <label class="form-label">دسترسی‌ها</label>
                    <?php foreach ($permissions as $perm): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>" id="perm_<?= $perm['id'] ?>">
                            <label class="form-check-label" for="perm_<?= $perm['id'] ?>">
                                <?= Sanitize::e($perm['description'] ?? $perm['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>افزودن
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="admin-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>توضیحات</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?= $role['id'] ?></td>
                                <td class="fw-bold"><span class="badge bg-info"><?= Sanitize::e($role['name']) ?></span></td>
                                <td class="small"><?= Sanitize::e($role['description'] ?? '') ?></td>
                                <td>
                                    <a href="/admin/roles/<?= $role['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" action="/admin/roles/<?= $role['id'] ?>/delete" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($roles)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">نقشی تعریف نشده</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
