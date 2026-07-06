<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<h4 class="fw-bold mb-4">مدیریت دسته‌بندی پست‌ها</h4>

<div class="row g-4">
    <div class="col-md-5">
        <div class="admin-form">
            <h6 class="fw-bold mb-3">افزودن دسته‌بندی جدید</h6>
            <form method="post" action="/admin/post-categories">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label class="form-label">نام دسته‌بندی</label>
                    <input type="text" class="form-control" name="name" required>
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
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td><?= $cat['id'] ?></td>
                                <td class="fw-bold"><?= Sanitize::e($cat['name']) ?></td>
                                <td>
                                    <form method="post" action="/admin/post-categories/<?= $cat['id'] ?>/delete" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">دسته‌بندی وجود ندارد</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
