<?php

namespace App\Controllers;

use App\Models\Product;
use App\Helpers\Sanitize;

class SearchController
{
    public function index(): void
    {
        $query = Sanitize::string($_GET['q'] ?? '');
        $results = !empty($query) ? Product::search($query, 50) : [];

        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/products/search.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }
}
