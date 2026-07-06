<?php use App\Helpers\Sanitize; use App\Middleware\Csrf; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">مدیریت محصولات</h4>
    <a href="/admin/products/create" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>افزودن محصول
    </a>
</div>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>تصویر</th>
                    <th>نام</th>
                    <th>قیمت</th>
                    <th>موجودی</th>
                    <th>دسته‌بندی</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php
                            $photoUrl = !empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')
                                ? '/' . $product['photo']
                                : '/img/' . ($product['photo'] ?? '');
                            ?>
                            <img src="<?= htmlspecialchars($photoUrl) ?>" width="50" height="50" style="object-fit: cover; border-radius: 6px;" alt="">
                        </td>
                        <td class="fw-bold"><?= Sanitize::e($product['name']) ?></td>
                        <td><?= number_format($product['price']) ?></td>
                        <td>
                            <?php if (($product['stock'] ?? 0) > 5): ?>
                                <span class="badge bg-success"><?= $product['stock'] ?></span>
                            <?php elseif (($product['stock'] ?? 0) > 0): ?>
                                <span class="badge bg-warning text-dark"><?= $product['stock'] ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">0</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $product['category_product_id'] ?? '-' ?></td>
                        <td>
                            <?php if (($product['is_active'] ?? 1) == 1): ?>
                                <span class="badge bg-success">فعال</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/products/<?= $product['id'] ?>/edit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="post" action="/admin/products/<?= $product['id'] ?>/delete" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Csrf::token()) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">محصولی وجود ندارد</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
