<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;
use App\Helpers\Upload;

class CategoryController
{
    public function index(): void
    {
        $categories = Category::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/products/categories.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function store(): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/categories');
            exit;
        }

        $name = Sanitize::string($_POST['name'] ?? '');

        if (empty($name)) {
            Auth::flash('danger', 'نام دسته بندی را وارد کنید');
            header('Location: /admin/categories');
            exit;
        }

        $photoPath = '';
        if (!empty($_FILES['photo']['name'])) {
            $photoPath = Upload::upload($_FILES['photo'], 'categories') ?? '';
        }

        Category::create(['name' => $name, 'photo' => $photoPath]);

        Auth::flash('success', 'دسته بندی با موفقیت ایجاد شد');
        header('Location: /admin/categories');
        exit;
    }

    public function delete(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/categories');
            exit;
        }

        Category::delete(Sanitize::int($id));
        Auth::flash('success', 'دسته بندی حذف شد');
        header('Location: /admin/categories');
        exit;
    }
}
