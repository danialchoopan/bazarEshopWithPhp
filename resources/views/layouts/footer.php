<footer class="site-footer mt-5">
    <div class="container">
        <div class="row py-4">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold mb-3"><?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?></h5>
                <p class="text-muted small">فروشگاه آنلاین با بهترین محصولات و خدمات</p>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">لینک‌ها</h6>
                <ul class="list-unstyled small">
                    <li><a href="/" class="text-muted text-decoration-none">صفحه اصلی</a></li>
                    <li><a href="/products" class="text-muted text-decoration-none">محصولات</a></li>
                    <li><a href="/blog" class="text-muted text-decoration-none">بلاگ</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">حساب کاربری</h6>
                <ul class="list-unstyled small">
                    <li><a href="/login" class="text-muted text-decoration-none">ورود</a></li>
                    <li><a href="/register" class="text-muted text-decoration-none">ثبت نام</a></li>
                    <li><a href="/orders" class="text-muted text-decoration-none">سفارشات من</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">تماس با ما</h6>
                <ul class="list-unstyled small text-muted">
                    <li><i class="bi bi-envelope me-2"></i>info@example.com</li>
                    <li><i class="bi bi-phone me-2"></i>021-12345678</li>
                </ul>
            </div>
        </div>
        <hr>
        <p class="text-center text-muted small py-2 mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($_ENV['APP_NAME'] ?? 'بازار') ?>. تمامی حقوق محفوظ است.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
