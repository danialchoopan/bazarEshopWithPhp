<?php use App\Helpers\Sanitize; ?>

<?php if ($post): ?>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="/" class="text-decoration-none">خانه</a></li>
        <li class="breadcrumb-item"><a href="/blog" class="text-decoration-none">بلاگ</a></li>
        <li class="breadcrumb-item active"><?= Sanitize::e($post['title']) ?></li>
    </ol>
</nav>

<article>
    <h2 class="fw-bold mb-3"><?= Sanitize::e($post['title']) ?></h2>

    <div class="text-muted small mb-4">
        <i class="bi bi-calendar me-1"></i><?= date('Y/m/d', strtotime($post['created_at'] ?? 'now')) ?>
        <?php if (!empty($post['category'])): ?>
            <span class="mx-2">|</span>
            <i class="bi bi-tag me-1"></i><?= Sanitize::e($post['category']['name']) ?>
        <?php endif; ?>
    </div>

    <?php if (!empty($post['photo']) && str_starts_with($post['photo'], 'uploads/')): ?>
        <img src="/<?= htmlspecialchars($post['photo']) ?>" class="img-fluid rounded mb-4" alt="<?= Sanitize::e($post['title']) ?>" style="max-height: 400px; width: 100%; object-fit: cover;">
    <?php endif; ?>

    <div class="post-body" style="line-height: 1.8; font-size: 1.05rem;">
        <?= $post['body'] ?>
    </div>
</article>
<?php else: ?>
<div class="text-center py-5">
    <p class="text-muted">پست یافت نشد</p>
    <a href="/blog" class="btn btn-primary btn-sm">بازگشت به بلاگ</a>
</div>
<?php endif; ?>
