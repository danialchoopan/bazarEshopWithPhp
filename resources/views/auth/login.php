<?php use App\Helpers\Sanitize; ?>

<div class="auth-card">
    <h4 class="fw-bold text-center mb-4">ورود به حساب کاربری</h4>

    <form method="post" action="/login">
        <div class="mb-3">
            <label for="email" class="form-label">ایمیل</label>
            <input type="email" class="form-control" id="email" name="email" required
                   value="<?= Sanitize::e($_POST['email'] ?? '') ?>" placeholder="email@example.com">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">رمز عبور</label>
            <input type="password" class="form-control" id="password" name="password" required
                   placeholder="رمز عبور">
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">ورود</button>

        <p class="text-center text-muted small">
            حساب کاربری ندارید؟
            <a href="/register" class="text-decoration-none">ثبت نام کنید</a>
        </p>
    </form>
</div>
