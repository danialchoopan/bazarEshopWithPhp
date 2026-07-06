<?php use App\Helpers\Sanitize; ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="/" class="text-decoration-none">خانه</a></li>
        <li class="breadcrumb-item active">درباره ما</li>
    </ol>
</nav>

<div class="row g-5">
    <div class="col-lg-7">
        <h2 class="fw-bold mb-4">درباره <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?></h2>
        <p class="text-muted" style="line-height: 2;">
            ما یک تیم کوچک اما پرشور هستیم که با هدف ارائه بهترین محصولات و تجربه خرید آنلاین راحت و مطمئن شروع به کار کردیم.
            باور ما این است که خرید آنلاین باید ساده، سریع و لذت‌بخش باشد.
        </p>
        <p class="text-muted" style="line-height: 2;">
            این پروژه در ابتدا یکی از اولین تجربه‌های من در برنامه‌نویسی PHP بود و بعد از چند سال تصمیم گرفتم با ساختاری مدرن و اصولی بازنویسی‌اش کنم.
            هدف من از این بازنویسی، یادگیری و بهبود مهارت‌هایم در توسعه وب بود و امیدوارم نتیجه کار مفید باشد.
        </p>

        <div class="row g-3 mt-4">
            <div class="col-6">
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(79,70,229,0.05);">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="fw-bold">تیم ما</div>
                        <small class="text-muted">توسعه‌دهندگان مشتاق</small>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(16,185,129,0.05);">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-code-slash"></i>
                    </div>
                    <div>
                        <div class="fw-bold">تکنولوژی</div>
                        <small class="text-muted">PHP, MySQL, Bootstrap</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">تماس با ما</h5>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div>
                        <small class="text-muted">ایمیل</small>
                        <div>info@example.com</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div>
                        <small class="text-muted">تلفن</small>
                        <div>021-12345678</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div>
                        <small class="text-muted">آدرس</small>
                        <div>تهران، خیابان ولیعصر</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
