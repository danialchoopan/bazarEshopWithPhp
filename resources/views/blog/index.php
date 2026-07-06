<?php use App\Helpers\Sanitize; ?>

<h4 class="fw-bold mb-4"><i class="bi bi-journal-text me-2"></i>بلاگ</h4>

<?php if (empty($posts)): ?>
    <div class="text-center py-5">
        <p class="text-muted">پستی منتشر نشده است</p>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-lg-4">
                <a href="/blog/<?= $post['id'] ?>" class="text-decoration-none">
                    <div class="blog-card h-100">
                        <?php if (!empty($post['photo']) && str_starts_with($post['photo'], 'uploads/')): ?>
                            <img src="/<?= htmlspecialchars($post['photo']) ?>" class="card-img-top" alt="<?= Sanitize::e($post['title']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h6 class="card-title fw-bold" style="color: var(--text);"><?= Sanitize::e($post['title']) ?></h6>
                            <p class="card-text text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= Sanitize::e(strip_tags($post['body'])) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <small class="text-muted"><?= date('Y/m/d', strtotime($post['created_at'] ?? 'now')) ?></small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
