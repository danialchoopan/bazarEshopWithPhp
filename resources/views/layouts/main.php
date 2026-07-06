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
            </ul>
            <form class="d-flex me-3" action="/search" method="get">
                <input class="form-control form-control-sm" type="search" name="q" placeholder="جستجو..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button class="btn btn-sm btn-outline-primary ms-1" type="submit"><i class="bi bi-search"></i></button>
            </form>
            <div class="d-flex align-items-center gap-2">
                <?php if ($_user): ?>
                    <a href="/cart" class="btn btn-outline-primary btn-sm position-relative">
                        <i class="bi bi-cart3"></i> سبد خرید
                        <?php if ($_cartCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $_cartCount ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <?= htmlspecialchars($_user['name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile">پروفایل</a></li>
                            <li><a class="dropdown-item" href="/orders">سفارشات من</a></li>
                            <?php if (($_user['role'] ?? 0) == 1): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/admin">داشبورد مدیریت</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout">خروج</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/login" class="btn btn-outline-primary btn-sm">ورود</a>
                    <a href="/register" class="btn btn-primary btn-sm">ثبت نام</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?php if ($_flash): ?>
        <div class="alert alert-<?= $_flash['type'] === 'danger' ? 'danger' : ($_flash['type'] === 'success' ? 'success' : ($_flash['type'] === 'warning' ? 'warning' : 'info')) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>
