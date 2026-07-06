<?php use App\Helpers\Sanitize; ?>

<div class="auth-card">
    <h4 class="fw-bold text-center mb-4">ثبت نام</h4>

    <form method="post" action="/register">
        <div class="row g-3">
            <div class="col-6">
                <label for="name" class="form-label">نام <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required
                       value="<?= Sanitize::e($_POST['name'] ?? '') ?>">
            </div>
            <div class="col-6">
                <label for="last_name" class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" required
                       value="<?= Sanitize::e($_POST['last_name'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label for="phone" class="form-label">شماره تلفن <span class="text-danger">*</span></label>
            <input type="tel" class="form-control" id="phone" name="phone" required
                   value="<?= Sanitize::e($_POST['phone'] ?? '') ?>" placeholder="09123456789">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">ایمیل <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required
                   value="<?= Sanitize::e($_POST['email'] ?? '') ?>" placeholder="email@example.com">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">رمز عبور <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password" name="password" required
                   minlength="6" placeholder="حداقل 6 کاراکتر">
        </div>

        <div class="mb-3">
            <label for="password_confirm" class="form-label">تکرار رمز عبور <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">ثبت نام</button>

        <p class="text-center text-muted small">
            قبلا ثبت نام کرده‌اید؟
            <a href="/login" class="text-decoration-none">وارد شوید</a>
        </p>
    </form>
</div>
