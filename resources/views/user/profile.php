<?php use App\Helpers\Sanitize; ?>

<h4 class="fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>پروفایل</h4>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-muted small">نام</label>
                <div class="fw-bold"><?= Sanitize::e($user['name']) ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">نام خانوادگی</label>
                <div class="fw-bold"><?= Sanitize::e($user['last_name']) ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">ایمیل</label>
                <div class="fw-bold"><?= Sanitize::e($user['email']) ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">شماره تلفن</label>
                <div class="fw-bold"><?= Sanitize::e($user['phone']) ?></div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">نقش</label>
                <div class="fw-bold">
                    <?php if (($user['role'] ?? 0) == 1): ?>
                        <span class="badge bg-primary">مدیر</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">کاربر</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">تاریخ عضویت</label>
                <div class="fw-bold"><?= date('Y/m/d', strtotime($user['created_at'] ?? 'now')) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="/orders" class="btn btn-outline-primary">
        <i class="bi bi-receipt me-1"></i>مشاهده سفارشات من
    </a>
</div>
