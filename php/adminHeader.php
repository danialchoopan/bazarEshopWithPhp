<?php
require_once 'inc/mainInc.php';
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo APP_NAME ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL ?>css/style.css">
</head>
<body>
<div class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">داشبورد <?php echo APP_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL . 'admin/index.php' ?>">داشبورد</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL . 'admin/user/users.php' ?>">کاربران</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            محصولات
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/product/addProduct.php' ?>">افزودن محصول</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/product/products.php' ?>">تمامی محصولات</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/product/category/index.php' ?>">دسته بندی ها</a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            بلاگ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown1">
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/post/create.php' ?>">افزودن پست </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/post/index.php' ?>">نمایش پست ها</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/post/category/index.php' ?>">دسته بندی ها</a>
                            </li>

                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            سفارش ها
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown2">
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/orders/orders.php' ?>">تمامی سفارش ها</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/orders/valid_orders.php' ?>">سفارش های تایید
                                    شده</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item"
                                   href="<?php echo APP_URL . 'admin/orders/invalid_orders.php' ?>">سفارش تایید نشده</a>
                            </li>

                        </ul>
                    </li>

                </ul>
                <div class="d-flex">

                    <a class="nav-link text-decoration-none" href="<?php echo APP_URL . 'index.php?logoutUser=1' ?>">خروج
                        (<?php echo checkIfUserLogin()['name'] ?>)</a>
                </div>
            </div>
        </div>
    </nav>