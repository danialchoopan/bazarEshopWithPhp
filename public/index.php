<?php

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Load .env (simple parser)
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize Router
use App\Config\Router;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\BlogController;
use App\Controllers\SearchController;
use App\Controllers\AboutController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\OrderController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\PostController;
use App\Controllers\Admin\CategoryController;
use App\Controllers\Admin\PostCategoryController;
use App\Controllers\Admin\RoleController;
use App\Middleware\Auth;

$router = new Router();

// Public routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/product/{id}', [ProductController::class, 'show']);
$router->get('/category/{id}', [ProductController::class, 'byCategory']);
$router->get('/search', [SearchController::class, 'index']);

// Cart
$router->get('/cart', [CartController::class, 'index'], [Auth::class, 'requireLogin']);
$router->post('/cart/add', [CartController::class, 'add'], [Auth::class, 'requireLogin']);
$router->post('/cart/update', [CartController::class, 'update'], [Auth::class, 'requireLogin']);
$router->post('/cart/remove', [CartController::class, 'remove'], [Auth::class, 'requireLogin']);

// Checkout
$router->get('/checkout', [CheckoutController::class, 'index'], [Auth::class, 'requireLogin']);
$router->post('/checkout', [CheckoutController::class, 'placeOrder'], [Auth::class, 'requireLogin']);

// Auth
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// User
$router->get('/profile', [UserController::class, 'profile'], [Auth::class, 'requireLogin']);
$router->get('/orders', [UserController::class, 'orders'], [Auth::class, 'requireLogin']);

// Blog
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/{id}', [BlogController::class, 'show']);

// About
$router->get('/about', [AboutController::class, 'index']);

// Admin routes
$router->get('/admin', [DashboardController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->get('/admin/dashboard', [DashboardController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->get('/admin/dashboard/stats', [DashboardController::class, 'stats'], [Auth::class, 'requireAdmin']);

// Admin Products
$router->get('/admin/products', [AdminProductController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->get('/admin/products/create', [AdminProductController::class, 'create'], [Auth::class, 'requireAdmin']);
$router->post('/admin/products', [AdminProductController::class, 'store'], [Auth::class, 'requireAdmin']);
$router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit'], [Auth::class, 'requireAdmin']);
$router->post('/admin/products/{id}/update', [AdminProductController::class, 'update'], [Auth::class, 'requireAdmin']);
$router->post('/admin/products/{id}/delete', [AdminProductController::class, 'delete'], [Auth::class, 'requireAdmin']);

// Admin Product Categories
$router->get('/admin/categories', [CategoryController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->post('/admin/categories', [CategoryController::class, 'store'], [Auth::class, 'requireAdmin']);
$router->post('/admin/categories/{id}/delete', [CategoryController::class, 'delete'], [Auth::class, 'requireAdmin']);

// Admin Orders
$router->get('/admin/orders', [OrderController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->get('/admin/orders/{id}', [OrderController::class, 'show'], [Auth::class, 'requireAdmin']);
$router->post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'], [Auth::class, 'requireAdmin']);

// Admin Users
$router->get('/admin/users', [AdminUserController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->get('/admin/users/{id}', [AdminUserController::class, 'show'], [Auth::class, 'requireAdmin']);
$router->post('/admin/users/{id}/role', [AdminUserController::class, 'updateRole'], [Auth::class, 'requireAdmin']);

// Admin Blog Posts
$router->get('/admin/posts', [PostController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->get('/admin/posts/create', [PostController::class, 'create'], [Auth::class, 'requireAdmin']);
$router->post('/admin/posts', [PostController::class, 'store'], [Auth::class, 'requireAdmin']);
$router->get('/admin/posts/{id}/edit', [PostController::class, 'edit'], [Auth::class, 'requireAdmin']);
$router->post('/admin/posts/{id}/update', [PostController::class, 'update'], [Auth::class, 'requireAdmin']);
$router->post('/admin/posts/{id}/delete', [PostController::class, 'delete'], [Auth::class, 'requireAdmin']);

// Admin Post Categories
$router->get('/admin/post-categories', [PostCategoryController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->post('/admin/post-categories', [PostCategoryController::class, 'store'], [Auth::class, 'requireAdmin']);
$router->post('/admin/post-categories/{id}/delete', [PostCategoryController::class, 'delete'], [Auth::class, 'requireAdmin']);

// Admin Roles
$router->get('/admin/roles', [RoleController::class, 'index'], [Auth::class, 'requireAdmin']);
$router->post('/admin/roles', [RoleController::class, 'store'], [Auth::class, 'requireAdmin']);
$router->get('/admin/roles/{id}/edit', [RoleController::class, 'edit'], [Auth::class, 'requireAdmin']);
$router->post('/admin/roles/{id}/update', [RoleController::class, 'update'], [Auth::class, 'requireAdmin']);
$router->post('/admin/roles/{id}/delete', [RoleController::class, 'delete'], [Auth::class, 'requireAdmin']);

// Dispatch
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);
