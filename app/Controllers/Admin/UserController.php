<?php

namespace App\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;

class UserController
{
    public function index(): void
    {
        $users = User::all(200);

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/users/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function show(string $id): void
    {
        $user = User::find(Sanitize::int($id));
        if (!$user) {
            header('Location: /admin/users');
            exit;
        }

        $orders = Order::findByUser($user['id']);
        $userRoles = User::getRoles($user['id']);
        $allRoles = Role::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/users/show.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function updateRole(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/users');
            exit;
        }

        $userId = Sanitize::int($id);
        $roleId = Sanitize::int($_POST['role_id'] ?? 0);

        // Update legacy role field
        User::update($userId, ['role' => $roleId >= 1 ? 1 : 0]);

        Auth::flash('success', 'نقش کاربر بروزرسانی شد');
        header('Location: /admin/users/' . $userId);
        exit;
    }
}
