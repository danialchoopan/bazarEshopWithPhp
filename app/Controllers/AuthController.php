<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;

class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/auth/login.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function login(): void
    {
        $email = Sanitize::email($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Auth::flash('danger', 'ایمیل و رمز عبور را وارد کنید');
            header('Location: /login');
            exit;
        }

        $user = User::authenticate($email, $password);

        if ($user) {
            Auth::login($user);
            Auth::flash('success', 'خوش آمدید');
            header('Location: /');
            exit;
        }

        Auth::flash('danger', 'ایمیل یا رمز عبور اشتباه است');
        header('Location: /login');
        exit;
    }

    public function registerForm(): void
    {
        if (Auth::check()) {
            header('Location: /');
            exit;
        }

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/auth/register.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function register(): void
    {
        $name = Sanitize::string($_POST['name'] ?? '');
        $lastName = Sanitize::string($_POST['last_name'] ?? '');
        $phone = Sanitize::phone($_POST['phone'] ?? '');
        $email = Sanitize::email($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validate
        $validation = Sanitize::validate($_POST, [
            'name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'phone' => 'required|phone',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                Auth::flash('danger', $error);
                break;
            }
            header('Location: /register');
            exit;
        }

        if ($password !== $passwordConfirm) {
            Auth::flash('danger', 'رمز عبور و تکرار آن مطابقت ندارند');
            header('Location: /register');
            exit;
        }

        // Check duplicate email
        if (User::findByEmail($email)) {
            Auth::flash('danger', 'این ایمیل قبلا ثبت شده است');
            header('Location: /register');
            exit;
        }

        $userId = User::create([
            'name' => $name,
            'last_name' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'password' => $password,
        ]);

        // Auto-login
        $user = User::find($userId);
        unset($user['password']);
        Auth::login($user);

        Auth::flash('success', 'ثبت نام شما با موفقیت انجام شد');
        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /');
        exit;
    }
}
