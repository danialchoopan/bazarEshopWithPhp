<?php use App\Helpers\Sanitize; use App\Models\Post; ?>

<!-- Hero Section -->
<div class="hero-section text-center">
    <h1>به <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?> خوش آمدید</h1>
    <p class="mb-4">بهترین محصولات با بهترین کیفیت و قیمت — ارسال سریع به سراسر کشور</p>
    <a href="/products" class="btn btn-light btn-lg px-5">مشاهده محصولات</a>
</div>

<!-- Features -->
<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="text-center p-3">
            <div class="feature-icon mx-auto mb-2" style="width: 56px; height: 56px; border-radius: 14px; background: rgba(79,70,229,0.1); color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-truck"></i>
            </div>
            <h6 class="fw-bold mb-1">ارسال سریع</h6>
            <small class="text-muted">ارسال به سراسر کشور</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="text-center p-3">
            <div class="feature-icon mx-auto mb-2" style="width: 56px; height: 56px; border-radius: 14px; background: rgba(16,185,129,0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-shield-check"></i>
            </div>
            <h6 class="fw-bold mb-1">ضمانت اصالت</h6>
            <small class="text-muted">تضمین اصالت محصولات</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="text-center p-3">
            <div class="feature-icon mx-auto mb-2" style="width: 56px; height: 56px; border-radius: 14px; background: rgba(245,158,11,0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-headset"></i>
            </div>
            <h6 class="fw-bold mb-1">پشتیبانی ۲۴/۷</h6>
            <small class="text-muted">پشتیبانی در تمام ساعات</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="text-center p-3">
            <div class="feature-icon mx-auto mb-2" style="width: 56px; height: 56px; border-radius: 14px; background: rgba(239,68,68,0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="bi bi-arrow-return-left"></i>
            </div>
            <h6 class="fw-bold mb-1">بازگشت آسان</h6>
            <small class="text-muted">۷ روز ضمانت بازگشت</small>
        </div>
    </div>
</div>

<!-- Categories -->
<?php if (!empty($categories)): ?>
<div class="mb-5">
    <div class="section-header">
        <h4 class="fw-bold mb-0">دسته‌بندی‌ها</h4>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <?php foreach ($categories as $cat): ?>
            <a href="/category/<?= $cat['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <?= Sanitize::e($cat['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Latest Products -->
<div class="mb-5">
    <div class="section-header">
        <h4 class="fw-bold mb-0">جدیدترین محصولات</h4>
        <a href="/products" class="btn btn-outline-primary btn-sm">مشاهده همه <i class="bi bi-arrow-left me-1"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="/product/<?= $product['id'] ?>" class="text-decoration-none">
                    <div class="product-card h-100">
                        <?php
                        $photoUrl = !empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')
                            ? '/' . $product['photo']
                            : '/img/' . ($product['photo'] ?? 'placeholder.png');
                        ?>
                        <img src="<?= htmlspecialchars($photoUrl) ?>" class="card-img-top" alt="<?= Sanitize::e($product['name']) ?>">
                        <div class="card-body">
                            <div class="product-title"><?= Sanitize::e($product['name']) ?></div>
                            <div class="product-price"><?= number_format($product['price']) ?> تومان</div>
                            <?php if (($product['stock'] ?? 0) > 0): ?>
                                <span class="badge badge-in-stock badge-stock">موجود</span>
                            <?php else: ?>
                                <span class="badge badge-out-of-stock badge-stock">ناموجود</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Blog Preview -->
<?php
$latestPosts = Post::latest(3);
if (!empty($latestPosts)):
?>
<div class="mb-5">
    <div class="section-header">
        <h4 class="fw-bold mb-0">آخرین مقالات</h4>
        <a href="/blog" class="btn btn-outline-primary btn-sm">مشاهده همه <i class="bi bi-arrow-left me-1"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($latestPosts as $post): ?>
            <div class="col-md-4">
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
                            <small class="text-muted"><i class="bi bi-calendar me-1"></i><?= date('Y/m/d', strtotime($post['created_at'] ?? 'now')) ?></small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Newsletter -->
<div class="rounded-3 p-4 mb-5 text-center" style="background: linear-gradient(135deg, #0f172a, #1e293b); color: white;">
    <h5 class="fw-bold mb-2">عضو خبرنامه شوید</h5>
    <p class="mb-3 opacity-75 small">از جدیدترین محصولات و تخفیف‌ها باخبر شوید</p>
    <form class="d-flex justify-content-center gap-2" style="max-width: 400px; margin: 0 auto;">
        <input type="email" class="form-control form-control-sm" placeholder="ایمیل خود را وارد کنید" style="border-radius: 10px;">
        <button type="button" class="btn btn-primary btn-sm px-3" style="border-radius: 10px;">عضویت</button>
    </form>
</div>
