<?php

namespace App\Controllers\Admin;

use App\Models\Post;
use App\Models\PostCategory;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;
use App\Helpers\Upload;

class PostController
{
    public function index(): void
    {
        $posts = Post::all(200);

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/posts/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function create(): void
    {
        $categories = PostCategory::all();
        $post = null;

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/posts/form.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function store(): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/posts');
            exit;
        }

        $title = Sanitize::string($_POST['title'] ?? '');
        $body = $_POST['body'] ?? '';
        $categoryId = Sanitize::int($_POST['category_id'] ?? 0);

        $validation = Sanitize::validate($_POST, [
            'title' => 'required|min:3',
        ]);

        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                Auth::flash('danger', $error);
                break;
            }
            header('Location: /admin/posts/create');
            exit;
        }

        $photoPath = '';
        if (!empty($_FILES['photo']['name'])) {
            $photoPath = Upload::upload($_FILES['photo'], 'posts');
        }

        Post::create([
            'title' => $title,
            'body' => $body,
            'photo' => $photoPath,
            'category_id' => $categoryId,
        ]);

        Auth::flash('success', 'پست با موفقیت ایجاد شد');
        header('Location: /admin/posts');
        exit;
    }

    public function edit(string $id): void
    {
        $post = Post::find(Sanitize::int($id));
        if (!$post) {
            header('Location: /admin/posts');
            exit;
        }

        $categories = PostCategory::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/posts/form.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function update(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/posts');
            exit;
        }

        $postId = Sanitize::int($id);
        $post = Post::find($postId);
        if (!$post) {
            header('Location: /admin/posts');
            exit;
        }

        $data = [
            'title' => Sanitize::string($_POST['title'] ?? ''),
            'body' => $_POST['body'] ?? '',
            'category_id' => Sanitize::int($_POST['category_id'] ?? 0),
        ];

        if (!empty($_FILES['photo']['name'])) {
            $photoPath = Upload::upload($_FILES['photo'], 'posts');
            if ($photoPath) {
                if (!empty($post['photo']) && str_starts_with($post['photo'], 'uploads/')) {
                    Upload::delete($post['photo']);
                }
                $data['photo'] = $photoPath;
            }
        }

        Post::update($postId, $data);

        Auth::flash('success', 'پست با موفقیت بروزرسانی شد');
        header('Location: /admin/posts');
        exit;
    }

    public function delete(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/posts');
            exit;
        }

        $postId = Sanitize::int($id);
        $post = Post::find($postId);

        if ($post && !empty($post['photo']) && str_starts_with($post['photo'], 'uploads/')) {
            Upload::delete($post['photo']);
        }

        Post::delete($postId);

        Auth::flash('success', 'پست با موفقیت حذف شد');
        header('Location: /admin/posts');
        exit;
    }
}
