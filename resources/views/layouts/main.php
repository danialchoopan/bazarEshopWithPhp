<?php
use App\Middleware\Auth;
use App\Models\Cart;

$_appName = $_ENV['APP_NAME'] ?? 'بازار';
$_baseUrl = rtrim($_ENV['APP_URL'] ?? '/', '/');
$_user = Auth::user();
$_cartCount = $_user ? Cart::count($_user['id']) : 0;
$_flash = Auth::getFlash();
$_csrfToken = \App\Middleware\Csrf::token();
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($_appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $_baseUrl ?>/css/style.css">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/"><?= htmlspecialchars($_appName) ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/">صفحه اصلی</a></li>
                <li class="nav-item"><a class="nav-link" href="/products">محصولات</a></li>
                <li class="nav-item"><a class="nav-link" href="/blog">بلاگ</a></li>
                <li class="nav-item"><a class="nav-link" href="/about">درباره ما</a></li>
            </ul>
            <form class="d-flex me-3" action="/search" method="get">
                <div class="input-group input-group-sm">
                    <input class="form-control" type="search" name="q" placeholder="جستجوی محصولات..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="border-radius: 10px 0 0 10px;">
                    <button class="btn btn-primary" type="submit" style="border-radius: 0 10px 10px 0;"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <div class="d-flex align-items-center gap-2">
                <?php if ($_user): ?>
                    <a href="/cart" class="btn btn-outline-primary btn-sm position-relative" style="border-radius: 10px;">
                        <i class="bi bi-cart3"></i> سبد خرید
                        <?php if ($_cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;"><?= $_cartCount ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" style="border-radius: 10px;">
                            <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_user['name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                            <li><a class="dropdown-item" href="/profile"><i class="bi bi-person me-2"></i>پروفایل</a></li>
                            <li><a class="dropdown-item" href="/orders"><i class="bi bi-receipt me-2"></i>سفارشات من</a></li>
                            <?php if (($_user['role'] ?? 0) == 1): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/admin"><i class="bi bi-speedometer2 me-2"></i>داشبورد مدیریت</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-left me-2"></i>خروج</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline-primary btn-sm" style="border-radius: 10px;">ورود</a>
                    <a href="/register" class="btn btn-primary btn-sm" style="border-radius: 10px;">ثبت نام</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Toast Notifications -->
<div class="container mt-4">
    <?php if ($_flash): ?>
        <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999; margin-top: 80px;">
            <div class="toast show" role="alert" id="mainToast" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="toast-body d-flex align-items-center gap-2 py-3 px-4">
                    <?php if ($_flash['type'] === 'success'): ?>
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <span class="fw-bold" style="color: #065f46;"><?= htmlspecialchars($_flash['message']) ?></span>
                    <?php elseif ($_flash['type'] === 'danger'): ?>
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-x-lg"></i>
                        </div>
                        <span class="fw-bold" style="color: #991b1b;"><?= htmlspecialchars($_flash['message']) ?></span>
                    <?php else: ?>
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <span class="fw-bold" style="color: #92400e;"><?= htmlspecialchars($_flash['message']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Scroll to Top -->
<button id="scrollToTop" class="btn btn-primary position-fixed" style="bottom: 2rem; left: 2rem; width: 44px; height: 44px; border-radius: 12px; display: none; z-index: 999; box-shadow: 0 4px 12px rgba(79,70,229,0.35);">
    <i class="bi bi-arrow-up"></i>
</button>

<script>
// Auto-dismiss toast after 4 seconds
document.addEventListener('DOMContentLoaded', function() {
    const toast = document.getElementById('mainToast');
    if (toast) {
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.parentElement.remove(), 300);
        }, 4000);
    }

    // Scroll to top button
    const scrollBtn = document.getElementById('scrollToTop');
    window.addEventListener('scroll', function() {
        scrollBtn.style.display = window.scrollY > 300 ? 'flex' : 'none';
    });
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});
</script>
