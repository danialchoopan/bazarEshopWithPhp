<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\Sanitize;

class ProductController
{
    public function index(): void
    {
        $page = Sanitize::int($_GET['page'] ?? 1);
        $pagination = Product::paginate($page, 12);
        $categories = Category::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/products/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function show(string $id): void
    {
        $product = Product::find(Sanitize::int($id));
        if (!$product) {
            header('Location: /products');
            exit;
        }

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/products/show.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }

    public function byCategory(string $id): void
    {
        $categoryId = Sanitize::int($id);
        $page = Sanitize::int($_GET['page'] ?? 1);
        $pagination = Product::paginate($page, 12, $categoryId);
        $category = Category::find($categoryId);
        $categories = Category::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/products/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }
}
