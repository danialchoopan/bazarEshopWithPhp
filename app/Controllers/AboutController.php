<?php

namespace App\Controllers;

class AboutController
{
    public function index(): void
    {
        require dirname(__DIR__, 2) . '/resources/views/layouts/main.php';
        require dirname(__DIR__, 2) . '/resources/views/about/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/footer.php';
    }
}
