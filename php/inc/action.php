<?php
if (isset($_POST['registerUser'])) {
    $name = $_POST['registerUser_name'];
    $last_name = $_POST['registerUser_last_name'];
    $phone = $_POST['registerUser_phone'];
    $email = $_POST['registerUser_email'];
    $password = $_POST['registerUser_password'];
    if (registerUser($name, $last_name, $phone, $email, $password)) {
        setMessageAlert("شما با موفقیت نام نویسی شده اید", true);
        header('location: index.php');
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}
//--------------
if (isset($_POST['loginUser'])) {
    $email = $_POST['loginUser_email'];
    $password = $_POST['loginUser_password'];
    if (loginUser($email, $password)) {
        setMessageAlert("شما با موفقیت وارد شده اید", true);
        header('location: index.php');
    } else {
        setMessageAlert("لطفا رمزعبور و نام کاربری خود را برسی کنید", false);
    }
}
//--------------
if (isset($_POST['addProductCategory'])) {
    $name = $_POST['ProductCategoryName'];
    $photo = $_FILES['ProductCategoryPhoto'];
    if (addProductCategory($name, $photo)) {
        setMessageAlert("دسته بندی شما با موفقیت افزوده شده", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}
if (isset($_POST['addProduct'])) {
    $name = $_POST['ProductName'];
    $price = $_POST['ProductPrice'];
    $description = $_POST['ProductDescription'];
    $category_id = $_POST['ProductCategory'];
    $photo = $_FILES['ProductPhoto'];
    if (addProduct($name, $price, $photo, $description, $category_id)) {
        setMessageAlert(" محصول شما با موفقیت افزوده شده", true);
        header('location: ' . APP_URL . 'admin/product/products.php');
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}
if (isset($_POST['updateProduct'])) {
    $product_id = $_POST['idProduct'];
    if (isset($_FILES['ProductPhoto']) && $_FILES['ProductPhoto']['name'] != "") {
        $name = $_POST['ProductName'];
        $price = $_POST['ProductPrice'];
        $description = $_POST['ProductDescription'];
        $category_id = $_POST['ProductCategory'];
        $photo = $_FILES['ProductPhoto'];
        if (updateProduct($product_id, $name, $price, $photo, $description, $category_id)) {
            setMessageAlert(" محصول شما با موفقیت افزوده شده", true);
            header('location: ' . APP_URL . 'admin/product/products.php');
        } else {
            setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
        }
    } else {
        $name = $_POST['ProductName'];
        $price = $_POST['ProductPrice'];
        $description = $_POST['ProductDescription'];
        $category_id = $_POST['ProductCategory'];
        $photo = 0;
        if (updateProduct($product_id, $name, $price, $photo, $description, $category_id)) {
            setMessageAlert(" محصول شما با موفقیت افزوده شده", true);
            header('location: ' . APP_URL . 'admin/product/products.php');
        } else {
            setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
        }
    }
}
if (isset($_POST['addBlogCategory'])) {
    $name = $_POST['ProductBlogName'];
    if (addBlogCategory($name)) {
        setMessageAlert("دسته بندی شما با موفقیت افزوده شده", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}


if (isset($_POST['addPostBlog'])) {
    $title = $_POST['BlogTitle'];
    $body = $_POST['BlogDescription'];
    $category_id = $_POST['BlogCategory'];
    $photo = $_FILES['BlogPhoto'];
    if (addBlogPost($title, $photo, $body, $category_id)) {
        setMessageAlert(" پست شما با موفقیت افزوده شده", true);
        header('location: ' . APP_URL . 'admin/post/index.php');
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}

if (isset($_POST['updatePostBlog'])) {
    $blogPostId = $_POST['blogPostId'];
    if (isset($_FILES['BlogPhoto']) && $_FILES['BlogPhoto']['name'] != "") {
        $name = $_POST['BlogTitle'];
        $description = $_POST['BlogDescription'];
        $category_id = $_POST['BlogCategory'];
        $photo = $_FILES['BlogPhoto'];
        if (updateBlogPost($blogPostId, $name, $photo, $description, $category_id)) {
            setMessageAlert("پست شما با موفقیت بروز شد", true);
            header('location: ' . APP_URL . 'admin/post/index.php');
        } else {
            setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
        }
    } else {
        $name = $_POST['BlogTitle'];
        $description = $_POST['BlogDescription'];
        $category_id = $_POST['BlogCategory'];
        $photo = 0;
        if (updateBlogPost($blogPostId, $name, $photo, $description, $category_id)) {
            setMessageAlert("پست شما با موفقیت بروز شد", true);
            header('location: ' . APP_URL . 'admin/post/index.php');
        } else {
            setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
        }
    }
}

//get
if (isset($_GET['logoutUser'])) {
    logoutUser();
    header('location: index.php');
}
//--------------
if (isset($_GET['deleteProductCategory'])) {
    $categoryID = $_GET['deleteProductCategory'];
    if (deleteProductCategory($categoryID)) {
        setMessageAlert("دسته بندی شما با موفقیت حذف شده", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}
if (isset($_GET['deleteProduct'])) {
    $product_id = $_GET['deleteProduct'];
    if (deleteProduct($product_id)) {
        setMessageAlert("محصول شما با موفقیت حذف شده", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}
if (isset($_GET['deleteBlogCategory'])) {
    $category_id = $_GET['deleteBlogCategory'];
    if (deleteBlogCategory($category_id)) {
        setMessageAlert("دسته بندی شما با موفقیت حذف شده", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}
if (isset($_GET['deleteBlogPostId'])) {
    $blogPostId = $_GET['deleteBlogPostId'];
    if (deleteBlogPosts($blogPostId)) {
        setMessageAlert("پست شما با موفقیت حذف شده", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}

if (isset($_GET['AddCartProduct_id'])) {
    $product_id = $_GET['AddCartProduct_id'];
    if (addToCart($product_id)) {
        setMessageAlert("محصول شما به سبد خرید اضافه شد", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
    header('location: ' . APP_URL . 'cart.php');
}

if (isset($_GET['deleteProductFromCart'])) {
    $cart_id = $_GET['deleteProductFromCart'];
    if (deleteFromCart($cart_id)) {
        setMessageAlert("محصول شما با موفقیت از سبد خرید حذف شد", true);
    } else {
        setMessageAlert("مشکلی پیش آمده است لطفا بعدا امتحان کنید", false);
    }
}