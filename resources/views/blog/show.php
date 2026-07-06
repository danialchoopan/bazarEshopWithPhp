<?php use App\Helpers\Sanitize; use App\Models\Post; use App\Models\PostCategory; ?>

<?php if ($post): ?>
<?php
$relatedPosts = Post::latest(4);
$relatedPosts = array_filter($relatedPosts, fn($p) => $p['id'] != $post['id']);
$relatedPosts = array_slice($relatedPosts, 0, 3);
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="/" class="text-decoration-none">خانه</a></li>
        <li class="breadcrumb-item"><a href="/blog" class="text-decoration-none">بلاگ</a></li>
        <li class="breadcrumb-item active"><?= Sanitize::e($post['title']) ?></li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-lg-8">
        <article>
            <h1 class="fw-bold mb-3" style="font-size: 1.8rem; line-height: 1.5;"><?= Sanitize::e($post['title']) ?></h1>

            <div class="d-flex align-items-center gap-3 mb-4 text-muted small">
                <span><i class="bi bi-calendar me-1"></i><?= date('Y/m/d', strtotime($post['created_at'] ?? 'now')) ?></span>
                <?php if (!empty($post['category'])): ?>
                    <span><i class="bi bi-tag me-1"></i><?= Sanitize::e($post['category']['name']) ?></span>
                <?php endif; ?>
                <span><i class="bi bi-clock me-1"></i><?= max(1, (int) ceil(str_word_count(strip_tags($post['body'])) / 200)) ?> دقیقه مطالعه</span>
            </div>

            <?php if (!empty($post['photo']) && str_starts_with($post['photo'], 'uploads/')): ?>
                <div class="mb-4" style="border-radius: 16px; overflow: hidden;">
                    <img src="/<?= htmlspecialchars($post['photo']) ?>" class="img-fluid" alt="<?= Sanitize::e($post['title']) ?>" style="width: 100%; max-height: 450px; object-fit: cover;">
                </div>
            <?php endif; ?>

            <div class="post-body" style="line-height: 2; font-size: 1.05rem; color: #334155;">
                <?= $post['body'] ?>
            </div>

            <!-- Share -->
            <div class="border-top mt-4 pt-4">
                <h6 class="fw-bold mb-3">اشتراک‌گذاری</h6>
                <div class="d-flex gap-2">
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://t.me/share/url?url=<?= urlencode($_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                        <i class="bi bi-telegram"></i>
                    </a>
                    <button onclick="navigator.clipboard.writeText(window.location.href); this.innerHTML='<i class=\'bi bi-check\'></i> کپی شد'" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                        <i class="bi bi-link-45deg"></i> کپی لینک
                    </button>
                </div>
            </div>
        </article>
    </div>

    <div class="col-lg-4">
        <!-- Related Posts -->
        <?php if (!empty($relatedPosts)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">مقاله‌های مرتبط</h6>
                <?php foreach ($relatedPosts as $rp): ?>
                    <a href="/blog/<?= $rp['id'] ?>" class="d-flex gap-3 text-decoration-none mb-3 pb-3 border-bottom">
                        <?php if (!empty($rp['photo']) && str_starts_with($rp['photo'], 'uploads/')): ?>
                            <img src="/<?= htmlspecialchars($rp['photo']) ?>" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px; flex-shrink: 0;" alt="">
                        <?php endif; ?>
                        <div>
                            <h6 class="mb-1 fw-bold small" style="color: #0f172a; line-height: 1.4;"><?= Sanitize::e($rp['title']) ?></h6>
                            <small class="text-muted"><?= date('Y/m/d', strtotime($rp['created_at'] ?? 'now')) ?></small>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Categories -->
        <?php $cats = PostCategory::all(); if (!empty($cats)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">دسته‌بندی‌ها</h6>
                <?php foreach ($cats as $cat): ?>
                    <a href="/blog?category=<?= $cat['id'] ?>" class="d-flex justify-content-between align-items-center text-decoration-none p-2 rounded mb-1" style="color: #475569; font-size: 0.9rem;">
                        <span><i class="bi bi-tag me-2"></i><?= Sanitize::e($cat['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<div class="text-center py-5">
    <i class="bi bi-journal-x" style="font-size: 4rem; color: #cbd5e1;"></i>
    <h5 class="text-muted mt-3">پست یافت نشد</h5>
    <a href="/blog" class="btn btn-primary btn-sm mt-2">بازگشت به بلاگ</a>
</div>
<?php endif; ?>
