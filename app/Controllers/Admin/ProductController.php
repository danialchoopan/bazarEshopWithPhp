<?php

namespace App\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use App\Middleware\Auth;
use App\Middleware\Csrf;
use App\Helpers\Sanitize;
use App\Helpers\Upload;

class ProductController
{
    public function index(): void
    {
        $products = Product::all(200, 0, false);
        $categories = Category::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/products/index.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function create(): void
    {
        $categories = Category::all();
        $product = null;

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/products/form.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function store(): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/products');
            exit;
        }

        $name = Sanitize::string($_POST['name'] ?? '');
        $price = Sanitize::price($_POST['price'] ?? 0);
        $description = Sanitize::string($_POST['description'] ?? '');
        $categoryId = Sanitize::int($_POST['category_id'] ?? 0);
        $stock = Sanitize::int($_POST['stock'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        $validation = Sanitize::validate($_POST, [
            'name' => 'required|min:2',
            'price' => 'required|numeric',
        ]);

        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                Auth::flash('danger', $error);
                break;
            }
            header('Location: /admin/products/create');
            exit;
        }

        $photoPath = '';
        if (!empty($_FILES['photo']['name'])) {
            $photoPath = Upload::upload($_FILES['photo'], 'products');
            if (!$photoPath) {
                Auth::flash('danger', 'خطا در آپلود تصویر');
                header('Location: /admin/products/create');
                exit;
            }
        }

        Product::create([
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'photo' => $photoPath,
            'category_product_id' => $categoryId,
            'stock' => $stock,
            'is_active' => $isActive,
        ]);

        Auth::flash('success', 'محصول با موفقیت ایجاد شد');
        header('Location: /admin/products');
        exit;
    }

    public function edit(string $id): void
    {
        $product = Product::find(Sanitize::int($id));
        if (!$product) {
            header('Location: /admin/products');
            exit;
        }

        $categories = Category::all();

        require dirname(__DIR__, 2) . '/resources/views/layouts/admin.php';
        require dirname(__DIR__, 2) . '/resources/views/admin/products/form.php';
        require dirname(__DIR__, 2) . '/resources/views/layouts/admin_footer.php';
    }

    public function update(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/products');
            exit;
        }

        $productId = Sanitize::int($id);
        $product = Product::find($productId);
        if (!$product) {
            header('Location: /admin/products');
            exit;
        }

        $data = [
            'name' => Sanitize::string($_POST['name'] ?? ''),
            'price' => Sanitize::price($_POST['price'] ?? 0),
            'description' => Sanitize::string($_POST['description'] ?? ''),
            'category_product_id' => Sanitize::int($_POST['category_id'] ?? 0),
            'stock' => Sanitize::int($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if (!empty($_FILES['photo']['name'])) {
            $photoPath = Upload::upload($_FILES['photo'], 'products');
            if ($photoPath) {
                // Delete old photo
                if (!empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')) {
                    Upload::delete($product['photo']);
                }
                $data['photo'] = $photoPath;
            }
        }

        Product::update($productId, $data);

        Auth::flash('success', 'محصول با موفقیت بروزرسانی شد');
        header('Location: /admin/products');
        exit;
    }

    public function delete(string $id): void
    {
        if (!Csrf::verify()) {
            Auth::flash('danger', 'خطای امنیتی');
            header('Location: /admin/products');
            exit;
        }

        $productId = Sanitize::int($id);
        $product = Product::find($productId);

        if ($product && !empty($product['photo']) && str_starts_with($product['photo'], 'uploads/')) {
            Upload::delete($product['photo']);
        }

        Product::delete($productId);

        Auth::flash('success', 'محصول با موفقیت حذف شد');
        header('Location: /admin/products');
        exit;
    }
}
