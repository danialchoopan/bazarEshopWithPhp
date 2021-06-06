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
            <a class="navbar-brand" href="#"><?php echo APP_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL ?>">صفحه اصلی</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL . '/products.php' ?>">تمامی محصولات</a>
                    </li>


                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            دسته بندی ها
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                            $productCategories = readAllProductCategories();
                            foreach ($productCategories as $productCategory) {
                                ?>
                                <li class="dropdown-item">
                                    <div class="row">
                                        <div class="col text-center">
                                            <a class="text-decoration-none " style="color: black"
                                               href="<?php echo APP_URL . 'category.php?category_id=' . $productCategory['id'] ?>"><?php echo $productCategory['name'] ?></a>
                                        </div>
                                        <div class="col">
                                            <img src="<?php echo APP_URL . 'img/' . $productCategory['photo'] ?>"
                                                 width="100%"
                                                 alt="">
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="">بلاگ</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo APP_URL . 'aboutus.php' ?>">درباره ما</a>
                    </li>
                    <?php
                    if (!checkIfUserLogin()) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL . 'user/login.php' ?>">ورود</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL . 'user/register.php' ?>">نام نویسی</a>
                        </li>
                        <?php
                    } else {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo APP_URL . 'user/profile.php' ?>">پروفایل
                                (<?php echo checkIfUserLogin()['name'] ?>)</a>
                        </li>
                    <?php } ?>


                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="جستجو" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">جستجو</button>
                </form>
            </div>
        </div>
    </nav>