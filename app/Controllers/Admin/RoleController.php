<?php

namespace App\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;

class RoleController
{
    public function index(): void
    {
        $roles = Role::all();
        $permissions = Permission::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/roles/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function store(): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/roles');
            exit;
        }

        $name = Sanitize::string($_POST['name'] ?? '');
        $description = Sanitize::string($_POST['description'] ?? '');

        if (empty($name)) {
            Auth::flash('danger', 'نام نقش را وارد کنید');
            header('Location: /admin/roles');
            exit;
        }

        $roleId = Role::create(['name' => $name, 'description' => $description]);

        // Assign permissions
        $permissionIds = $_POST['permissions'] ?? [];
        if (!empty($permissionIds)) {
            $permissionIds = array_map('intval', $permissionIds);
            Role::setPermissions($roleId, $permissionIds);
        }

        Auth::flash('success', 'نقش با موفقیت ایجاد شد');
        header('Location: /admin/roles');
        exit;
    }

    public function edit(string $id): void
    {
        $role = Role::find(Sanitize::int($id));
        if (!$role) {
            header('Location: /admin/roles');
            exit;
        }

        $permissions = Permission::all();
        $rolePermissions = Role::getPermissions($role['id']);
        $rolePermissionIds = array_column($rolePermissions, 'id');

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/roles/edit.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function update(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/roles');
            exit;
        }

        $roleId = Sanitize::int($id);
        $name = Sanitize::string($_POST['name'] ?? '');
        $description = Sanitize::string($_POST['description'] ?? '');

        Role::update($roleId, ['name' => $name, 'description' => $description]);

        $permissionIds = $_POST['permissions'] ?? [];
        $permissionIds = array_map('intval', $permissionIds);
        Role::setPermissions($roleId, $permissionIds);

        Auth::flash('success', 'نقش با موفقیت بروزرسانی شد');
        header('Location: /admin/roles');
        exit;
    }

    public function delete(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/roles');
            exit;
        }

        Role::delete(Sanitize::int($id));
        Auth::flash('success', 'نقش حذف شد');
        header('Location: /admin/roles');
        exit;
    }
}
