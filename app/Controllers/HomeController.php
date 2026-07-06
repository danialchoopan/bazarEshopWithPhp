<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController
{
    public function index(): void
    {
        $products = Product::latest(8);
        $categories = Category::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/home/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }
}
