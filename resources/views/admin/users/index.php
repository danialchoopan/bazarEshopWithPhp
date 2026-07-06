<?php use App\Helpers\Sanitize; ?>

<h4 class="fw-bold mb-4">مدیریت کاربران</h4>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>نام</th>
                    <th>ایمیل</th>
                    <th>تلفن</th>
                    <th>نقش</th>
                    <th>تاریخ عضویت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td class="fw-bold"><?= Sanitize::e($u['name'] . ' ' . $u['last_name']) ?></td>
                        <td class="small"><?= Sanitize::e($u['email']) ?></td>
                        <td class="small"><?= Sanitize::e($u['phone']) ?></td>
                        <td>
                            <?php if (($u['role'] ?? 0) == 1): ?>
                                <span class="badge bg-primary">مدیر</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">کاربر</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= date('Y/m/d', strtotime($u['created_at'] ?? 'now')) ?></td>
                        <td>
                            <a href="/admin/users/<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
