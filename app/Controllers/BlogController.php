<?php

namespace App\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Helpers\Sanitize;

class BlogController
{
    public function index(): void
    {
        $posts = Post::all(20);
        $categories = PostCategory::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/blog/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function show(string $id): void
    {
        $post = Post::find(Sanitize::int($id));
        if (!$post) {
            header('Location: /blog');
            exit;
        }

        $categories = PostCategory::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/blog/show.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }
}
