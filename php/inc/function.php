<?php

function registerUser($name, $last_name, $phone, $email, $password)
{
    global $db_connection;
    global $message;
    global $messageStatus;
    $create_at = time();
    $result = $db_connection->query("INSERT INTO `users`(`name`, `last_name`, `phone`, `email`, `password`, `create_at`) VALUES ('$name','$last_name','$phone','$email','$password','$create_at')");
    return $result->rowCount();
}

function loginUser($email, $password)
{
    global $db_connection;
    global $message;
    global $messageStatus;
    $result = $db_connection->query("SELECT * FROM `users` WHERE `email`='$email' AND `password`='$password'");
    if ($result->rowCount()) {
        $_SESSION['user'] = $result->fetch(2);
        return $_SESSION['user'];
    } else {
        $message = "نام کاربری یا رمزعبور خود را برسی کنید";
        $messageStatus = false;
        return false;
    }
}

function checkIfUserLogin()
{
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    } else {
        return false;
    }
}

function logoutUser()
{
    unset($_SESSION['user']);
}


function setMessageAlert($message, $messageStatus)
{
    $_SESSION['message'] = $message;
    $_SESSION['messageStatus'] = $messageStatus;
}

function getMessageAlert()
{
    if (isset($_SESSION['message']) && isset($_SESSION['messageStatus'])) {
        $message = $_SESSION['message'];
        $messageStatus = $_SESSION['messageStatus'];
        unset($_SESSION['message']);
        unset($_SESSION['messageStatus']);
        if ($messageStatus) {
            return "<div class='alert alert-success m-3' role='alert'>$message</div>";
        } else {
            return "<div class='alert alert-danger' role='alert'>$message</div>";
        }
    } else {
        return "";
    }
}

function redirectIfUserLogged()
{
    if (checkIfUserLogin()) {
        header('location: ' . APP_URL . 'index.php');
    }
}

function redirectIfUserNotLogged()
{
    if (!checkIfUserLogin()) {
        header('location: ' . APP_URL . 'user/login.php');
    }
}

function redirectIfUserNotLoggedAdmin()
{
    if (!checkIfUserLogin()) {
        header('location: ' . APP_URL . 'user/login.php');
    }
    if (!checkIfUserLogin()['role']) {
        header('location: ' . APP_URL . 'user/login.php');
    }
}

function readAllProductCategories()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `category_product` ");
    return $result->fetchAll();
}

function addProductCategory($name, $photo)
{
    global $db_connection;
    $create_at = time();
    $file_name = time() . $photo['name'];
    $tmp_photo = $photo['tmp_name'];
    move_uploaded_file($tmp_photo, dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name);
    $result = $db_connection->query("INSERT INTO `category_product`(`name`, `photo`, `create_at`) VALUES ('$name','$file_name','$create_at')");
    return $result->rowCount();
}

function deleteProductCategory($id)
{
    global $db_connection;
    $result = $db_connection->query("DELETE FROM `category_product` WHERE `id`='$id'");
    return $result->rowCount();
}

function addProduct($name, $price, $photo, $description, $category_id)
{
    global $db_connection;
    $create_at = time();
    $file_name = time() . $photo['name'];
    $tmp_photo = $photo['tmp_name'];
    move_uploaded_file($tmp_photo, dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name);
    $result = $db_connection->query("INSERT INTO `products`(`name`,`price`, `description`, `photo`, `category_product_id`, `created_at`) VALUES ('$name','$price','$description','$file_name','$category_id','$create_at')");
    return $result->rowCount();
}

function updateProduct($product_id, $name, $price, $photo, $description, $category_id)
{
    global $db_connection;
    if ($photo) {
        $file_name = time() . $photo['name'];
        $tmp_photo = $photo['tmp_name'];
        move_uploaded_file($tmp_photo, dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name);
        $result = $db_connection->query("UPDATE `products` SET `name`='$name',`price`=$price,`description`='$description',`photo`='$file_name',`category_product_id`='$category_id' WHERE `id`='$product_id'");
    } else {
        $result = $db_connection->query("UPDATE `products` SET `name`='$name',`price`=$price,`description`='$description',`category_product_id`='$category_id' WHERE `id`='$product_id'");
    }
    return $result->rowCount();
}

function readAllProduct()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `products` ");
    return $result->fetchAll();
}

function latestProducts()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `products` LIMIT 4");
    return $result->fetchAll();
}

function readAllProductByCategoryId($category_id)
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `products` WHERE `category_product_id`='$category_id'");
    return $result->fetchAll();
}


function getProductById($product_id)
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `products` WHERE `id`='$product_id'");
    return $result->fetch();
}

function getCategoryById($category_id)
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `category_product` WHERE `id`='$category_id'");
    return $result->fetch();
}

function getCategoryBlogById($category_id)
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `category_post` WHERE `id`='$category_id'");
    return $result->fetch();
}

function deleteProduct($id)
{
    global $db_connection;
    $result = $db_connection->query("DELETE FROM `products` WHERE `id`='$id'");
    return $result->rowCount();
}

function readAllUsers()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `users` ");
    return $result->fetchAll();
}

function readAllBlogCategories()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `category_post` ");
    return $result->fetchAll();
}

function addBlogCategory($name)
{
    global $db_connection;
    $create_at = time();
    $result = $db_connection->query("INSERT INTO `category_post`(`name`,`created_at`) VALUES ('$name','$create_at')");
    return $result->rowCount();
}

function deleteBlogCategory($id)
{
    global $db_connection;
    $result = $db_connection->query("DELETE FROM `category_post` WHERE `id`='$id'");
    return $result->rowCount();
}

function readAllBlogPosts()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `posts` ");
    return $result->fetchAll();
}

function getBlogPostsById($blogPost_id)
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `posts` WHERE `id`='$blogPost_id'");
    return $result->fetch();
}

function deleteBlogPosts($id)
{
    global $db_connection;
    $result = $db_connection->query("DELETE FROM `posts` WHERE `id`='$id'");
    return $result->rowCount();
}

function addBlogPost($title, $photo, $body, $category_id)
{
    global $db_connection;
    $create_at = time();
    $file_name = time() . $photo['name'];
    $tmp_photo = $photo['tmp_name'];
    move_uploaded_file($tmp_photo, dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name);
    $result = $db_connection->query("INSERT INTO `posts`(`title`, `body`, `photo`, `category_id`, `created_at`) VALUES ('$title','$body','$file_name','$category_id','$create_at')");
    return $result->rowCount();
}

function updateBlogPost($blogPost_id, $title, $photo, $body, $category_id)
{
    global $db_connection;
    if ($photo) {
        $file_name = time() . $photo['name'];
        $tmp_photo = $photo['tmp_name'];
        move_uploaded_file($tmp_photo, dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name);
        $result = $db_connection->query("UPDATE `posts` SET `title`='$title',`body`='$body',`photo`='$file_name',`category_id`='$category_id' WHERE `id`='$blogPost_id'");
    } else {
        $result = $db_connection->query("UPDATE `posts` SET `title`='$title',`body`='$body',`category_id`='$category_id' WHERE `id`='$blogPost_id'");
    }
    return $result->rowCount();
}

function addToCart($product_id)
{
    global $db_connection;
    $user_id = $_SESSION['user']['id'];
    $result = $db_connection->query("INSERT INTO `cart`(`user_id`, `product_id`) VALUES ('$user_id','$product_id')");
    return $result->rowCount();
}

function readUserCart()
{
    global $db_connection;
    $user_id = $_SESSION['user']['id'];
    $result = $db_connection->query("SELECT * FROM `cart` WHERE `user_id`='$user_id'");
    return $result->fetchAll();
}

function deleteFromCart($id)
{
    global $db_connection;
    $result = $db_connection->query("DELETE FROM `cart` WHERE `id`='$id'");
    return $result->rowCount();
}

function addOrder($phone, $userAddress, $description)
{
    global $db_connection;
    $user_id = $_SESSION['user']['id'];
    $resultCart = $db_connection->query("SELECT * FROM `cart` WHERE `user_id`='$user_id'");
    $productsCart = [];
    foreach ($resultCart->fetchAll() as $cart) {
        $productsCart[] = $cart['product_id'];
    }
    $products = serialize($productsCart);
    $db_connection->query("DELETE FROM `cart` WHERE `user_id`='$user_id'");
    $result = $db_connection->query("INSERT INTO `orders`(`user_id`, `products`, `status`, `user_address`, `description`,`phone`) VALUES ('$user_id','$products','0','$userAddress','$description','$phone')");
    return true;
}

function readAllUserOrders()
{
    global $db_connection;
    $user_id = $_SESSION['user']['id'];
    $result = $db_connection->query("SELECT * FROM `orders` WHERE `user_id`='$user_id'");
    return $result->fetchAll();
}

function getUserOrderById($order_id)
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `orders` WHERE `id`='$order_id'");
    return $result->fetch();
}

function searchProduct($search_query)
{
    global $db_connection;
    $user_id = $_SESSION['user']['id'];
    $result = $db_connection->query("SELECT * FROM `products` WHERE `name` LIKE '%$search_query%'");
    return $result->fetchAll();
}

function readAllOrders()
{
    global $db_connection;
    $result = $db_connection->query("SELECT * FROM `orders`");
    return $result->fetchAll();
}
