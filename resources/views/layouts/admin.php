<?php
use App\Middleware\Auth;

$_appName = $_ENV['APP_NAME'] ?? 'بازار';
$_baseUrl = rtrim($_ENV['APP_URL'] ?? '/', '/');
$_user = Auth::user();
$_flash = Auth::getFlash();
$_csrfToken = \App\Middleware\Csrf::token();
$_currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>داشبورد مدیریت - <?= htmlspecialchars($_appName) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= $_baseUrl ?>/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <!-- Sidebar -->
    <nav class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="/admin" class="sidebar-brand">
                <i class="bi bi-shop"></i>
                <span>داشبورد</span>
            </a>
        </div>
        <ul class="sidebar-nav">
            <li class="sidebar-nav-item <?= $_currentPath === '/admin' || $_currentPath === '/admin/dashboard' ? 'active' : '' ?>">
                <a href="/admin"><i class="bi bi-speedometer2"></i><span>داشبورد</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/products') ? 'active' : '' ?>">
                <a href="/admin/products"><i class="bi bi-box-seam"></i><span>محصولات</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/categories') ? 'active' : '' ?>">
                <a href="/admin/categories"><i class="bi bi-tags"></i><span>دسته‌بندی محصولات</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/orders') ? 'active' : '' ?>">
                <a href="/admin/orders"><i class="bi bi-receipt"></i><span>سفارشات</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/users') ? 'active' : '' ?>">
                <a href="/admin/users"><i class="bi bi-people"></i><span>کاربران</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/posts') ? 'active' : '' ?>">
                <a href="/admin/posts"><i class="bi bi-journal-text"></i><span>پست‌ها</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/post-categories') ? 'active' : '' ?>">
                <a href="/admin/post-categories"><i class="bi bi-bookmark"></i><span>دسته‌بندی پست‌ها</span></a>
            </li>
            <li class="sidebar-nav-item <?= str_starts_with($_currentPath, '/admin/roles') ? 'active' : '' ?>">
                <a href="/admin/roles"><i class="bi bi-shield-lock"></i><span>نقش‌ها</span></a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="/" target="_blank" class="sidebar-link"><i class="bi bi-eye"></i><span>مشاهده سایت</span></a>
            <a href="/logout" class="sidebar-link text-danger"><i class="bi bi-box-arrow-left"></i><span>خروج</span></a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-topbar d-flex justify-content-between align-items-center p-3 border-bottom">
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">خوش آمدید، <?= htmlspecialchars($_user['name'] ?? '') ?></span>
            </div>
        </div>

        <div class="admin-content p-4">
            <?php if ($_flash): ?>
                <div class="alert alert-<?= $_flash['type'] === 'danger' ? 'danger' : ($_flash['type'] === 'success' ? 'success' : 'warning') ?> alert-dismissible fade show">
                    <?= htmlspecialchars($_flash['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
