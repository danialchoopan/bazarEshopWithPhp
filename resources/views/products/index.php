<?php use App\Helpers\Sanitize; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <?= isset($category) ? Sanitize::e($category['name']) : 'تمامی محصولات' ?>
    </h4>
</div>

<!-- Category Filter -->
<div class="category-filter mb-4 d-flex flex-wrap gap-2">
    <a href="/products" class="btn <?= !isset($category) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">همه</a>
    <?php foreach ($categories as $cat): ?>
        <a href="/category/<?= $cat['id'] ?>"
           class="btn <?= (isset($category) && $category['id'] == $cat['id']) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
            <?= Sanitize::e($cat['name']) ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- Products Grid -->
<?php if (empty($pagination['items'])): ?>
    <div class="text-center py-5">
        <i class="bi bi-box-seam" style="font-size: 3rem; color: #cbd5e1;"></i>
        <p class="text-muted mt-3">محصولی یافت نشد</p>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($pagination['items'] as $product): ?>
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
                            <?php if (($product['stock'] ?? 0) > 5): ?>
                                <span class="badge badge-in-stock badge-stock">موجود</span>
                            <?php elseif (($product['stock'] ?? 0) > 0): ?>
                                <span class="badge badge-low-stock badge-stock">موجودی کم</span>
                            <?php else: ?>
                                <span class="badge badge-out-of-stock badge-stock">ناموجود</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?= $i == $pagination['page'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
