<?php

namespace App\Controllers\Admin;

use App\Models\PostCategory;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;

class PostCategoryController
{
    public function index(): void
    {
        $categories = PostCategory::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/posts/categories.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function store(): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/post-categories');
            exit;
        }

        $name = Sanitize::string($_POST['name'] ?? '');

        if (empty($name)) {
            Auth::flash('danger', 'نام دسته بندی را وارد کنید');
            header('Location: /admin/post-categories');
            exit;
        }

        PostCategory::create(['name' => $name]);

        Auth::flash('success', 'دسته بندی با موفقیت ایجاد شد');
        header('Location: /admin/post-categories');
        exit;
    }

    public function delete(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/post-categories');
            exit;
        }

        PostCategory::delete(Sanitize::int($id));
        Auth::flash('success', 'دسته بندی حذف شد');
        header('Location: /admin/post-categories');
        exit;
    }
}
