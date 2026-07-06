<footer class="site-footer mt-5">
    <div class="container">
        <div class="row py-5">
            <div class="col-md-4 mb-4">
                <h5 class="fw-bold mb-3" style="font-size: 1.3rem;"><?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?></h5>
                <p class="small" style="line-height: 1.8; opacity: 0.8;">فروشگاه آنلاین با بهترین محصولات و خدمات. ارسال سریع به سراسر کشور با ضمانت اصالت کالا.</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-sm" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.1); color: #94a3b8; display: flex; align-items: center; justify-content: center;"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="btn btn-sm" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.1); color: #94a3b8; display: flex; align-items: center; justify-content: center;"><i class="bi bi-telegram"></i></a>
                    <a href="#" class="btn btn-sm" style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.1); color: #94a3b8; display: flex; align-items: center; justify-content: center;"><i class="bi bi-twitter"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4">
                <h6 class="fw-bold mb-3">لینک‌های سریع</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/" class="text-decoration-none" style="color: #94a3b8;">صفحه اصلی</a></li>
                    <li class="mb-2"><a href="/products" class="text-decoration-none" style="color: #94a3b8;">محصولات</a></li>
                    <li class="mb-2"><a href="/blog" class="text-decoration-none" style="color: #94a3b8;">بلاگ</a></li>
                    <li class="mb-2"><a href="/about" class="text-decoration-none" style="color: #94a3b8;">درباره ما</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">حساب کاربری</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="/login" class="text-decoration-none" style="color: #94a3b8;">ورود</a></li>
                    <li class="mb-2"><a href="/register" class="text-decoration-none" style="color: #94a3b8;">ثبت نام</a></li>
                    <li class="mb-2"><a href="/orders" class="text-decoration-none" style="color: #94a3b8;">سفارشات من</a></li>
                    <li class="mb-2"><a href="/profile" class="text-decoration-none" style="color: #94a3b8;">پروفایل</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4">
                <h6 class="fw-bold mb-3">تماس با ما</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2" style="color: #94a3b8;"><i class="bi bi-envelope me-2"></i>info@example.com</li>
                    <li class="mb-2" style="color: #94a3b8;"><i class="bi bi-phone me-2"></i>021-12345678</li>
                    <li class="mb-2" style="color: #94a3b8;"><i class="bi bi-geo-alt me-2"></i>تهران، خیابان ولیعصر</li>
                </ul>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,0.1);">
        <div class="d-flex justify-content-between align-items-center py-3">
            <p class="small mb-0" style="color: #64748b;">&copy; <?= date('Y') ?> <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?>. تمامی حقوق محفوظ است.</p>
            <p class="small mb-0" style="color: #475569;">ساخته شده با <i class="bi bi-heart-fill text-danger"></i> با PHP</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
