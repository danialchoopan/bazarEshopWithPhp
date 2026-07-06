<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">مدیریت پست‌ها</h4>
    <a href="/admin/posts/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>افزودن پست
    </a>
</div>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>عنوان</th>
                    <th>دسته‌بندی</th>
                    <th>تاریخ</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= $post['id'] ?></td>
                        <td class="fw-bold"><?= Sanitize::e($post['title']) ?></td>
                        <td><?= $post['category_id'] ?></td>
                        <td class="small text-muted"><?= date('Y/m/d', strtotime($post['created_at'] ?? 'now')) ?></td>
                        <td>
                            <a href="/admin/posts/<?= $post['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="post" action="/admin/posts/<?= $post['id'] ?>/delete" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($posts)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">پستی وجود ندارد</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
