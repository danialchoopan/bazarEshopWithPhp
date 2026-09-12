<?php
/**
 * Core Application Functions
 * 
 * This file contains all the main business logic functions for the Bazar Shop application.
 * Functions are organized by category: User Management, Product Management, Cart, Orders, Blog, etc.
 * 
 * SECURITY NOTE: All database queries use prepared statements to prevent SQL injection.
 * 
 * @package BazarShop
 * @version 2.0
 */

// ============================================================================
// USER MANAGEMENT FUNCTIONS
// ============================================================================

/**
 * Register a new user account
 * 
 * Creates a new user in the database with hashed password for security.
 * Uses prepared statements to prevent SQL injection.
 * 
 * @param string $name User's first name
 * @param string $last_name User's last name
 * @param string $phone User's phone number
 * @param string $email User's email address (must be unique)
 * @param string $password User's password (will be hashed with bcrypt)
 * @return int Number of rows affected (1 if successful, 0 if failed)
 */
function registerUser($name, $last_name, $phone, $email, $password)
{
    global $db_connection;
    
    // Hash password using bcrypt for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $create_at = time();
    
    // Use prepared statement to prevent SQL injection
    $stmt = $db_connection->prepare("INSERT INTO users (name, last_name, phone, email, password, create_at) VALUES (:name, :last_name, :phone, :email, :password, :create_at)");
    $stmt->execute([
        ':name' => $name,
        ':last_name' => $last_name,
        ':phone' => $phone,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':create_at' => $create_at
    ]);
    
    return $stmt->rowCount();
}

/**
 * Authenticate user login
 * 
 * Verifies user credentials and creates session if valid.
 * Uses prepared statements and secure password verification.
 * 
 * @param string $email User's email address
 * @param string $password User's password (plain text, will be verified against hash)
 * @return array|false User data array if successful, false if failed
 */
function loginUser($email, $password)
{
    global $db_connection;
    global $message;
    global $messageStatus;
    
    // Use prepared statement to prevent SQL injection
    $stmt = $db_connection->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verify password hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            return $user;
        } else {
            $message = "نام کاربری یا رمزعبور خود را برسی کنید";
            $messageStatus = false;
            return false;
        }
    } else {
        $message = "نام کاربری یا رمزعبور خود را برسی کنید";
        $messageStatus = false;
        return false;
    }
}

/**
 * Check if user is currently logged in
 * 
 * @return array|false User data array if logged in, false otherwise
 */
function checkIfUserLogin()
{
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    } else {
        return false;
    }
}

/**
 * Logout current user
 * 
 * Destroys user session data
 */
function logoutUser()
{
    unset($_SESSION['user']);
}

// ============================================================================
// MESSAGE & ALERT FUNCTIONS
// ============================================================================

/**
 * Set a session message alert
 * 
 * Stores message in session for display on next page load.
 * Automatically cleared after being displayed.
 * 
 * @param string $message Message text to display
 * @param bool $messageStatus true for success, false for error
 */
function setMessageAlert($message, $messageStatus)
{
    $_SESSION['message'] = $message;
    $_SESSION['messageStatus'] = $messageStatus;
}

/**
 * Get and clear session message alert
 * 
 * Returns formatted HTML alert div if message exists in session.
 * Automatically clears the message after retrieving.
 * 
 * @return string HTML alert div or empty string
 */
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

// ============================================================================
// REDIRECTION HELPER FUNCTIONS
// ============================================================================

/**
 * Redirect to homepage if user is logged in
 * 
 * Used on login/register pages to prevent access when already authenticated
 */
function redirectIfUserLogged()
{
    if (checkIfUserLogin()) {
        header('location: ' . APP_URL . 'index.php');
        exit();
    }
}

/**
 * Redirect to login page if user is not logged in
 * 
 * Used to protect user-specific pages
 */
function redirectIfUserNotLogged()
{
    if (!checkIfUserLogin()) {
        header('location: ' . APP_URL . 'user/login.php');
        exit();
    }
}

/**
 * Redirect to login page if user is not logged in or not admin
 * 
 * Used to protect admin pages
 */
function redirectIfUserNotLoggedAdmin()
{
    $user = checkIfUserLogin();
    
    if (!$user) {
        header('location: ' . APP_URL . 'user/login.php');
        exit();
    }
    
    if (!isset($user['role']) || !$user['role']) {
        header('location: ' . APP_URL . 'user/login.php');
        exit();
    }
}

// ============================================================================
// PRODUCT CATEGORY FUNCTIONS
// ============================================================================

/**
 * Get all product categories
 * 
 * @return array Array of category records
 */
function readAllProductCategories()
{
    global $db_connection;
    $stmt = $db_connection->query("SELECT * FROM category_product ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Add a new product category
 * 
 * Handles file upload for category photo
 * 
 * @param string $name Category name
 * @param array $photo Uploaded file array from $_FILES
 * @return int Number of rows affected
 */
function addProductCategory($name, $photo)
{
    global $db_connection;
    
    $create_at = time();
    $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $photo['name']);
    $tmp_photo = $photo['tmp_name'];
    $uploadPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name;
    
    // Move uploaded file
    if (move_uploaded_file($tmp_photo, $uploadPath)) {
        $stmt = $db_connection->prepare("INSERT INTO category_product (name, photo, create_at) VALUES (:name, :photo, :create_at)");
        $stmt->execute([
            ':name' => $name,
            ':photo' => $file_name,
            ':create_at' => $create_at
        ]);
        return $stmt->rowCount();
    }
    
    return 0;
}

/**
 * Delete a product category
 * 
 * @param int $id Category ID
 * @return int Number of rows affected
 */
function deleteProductCategory($id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("DELETE FROM category_product WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

// ============================================================================
// PRODUCT FUNCTIONS
// ============================================================================

/**
 * Add a new product
 * 
 * Handles file upload for product photo
 * 
 * @param string $name Product name
 * @param float $price Product price
 * @param array $photo Uploaded file array from $_FILES
 * @param string $description Product description
 * @param int $category_id Category ID
 * @return int Number of rows affected
 */
function addProduct($name, $price, $photo, $description, $category_id)
{
    global $db_connection;
    
    $create_at = time();
    $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $photo['name']);
    $tmp_photo = $photo['tmp_name'];
    $uploadPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name;
    
    // Move uploaded file
    if (move_uploaded_file($tmp_photo, $uploadPath)) {
        $stmt = $db_connection->prepare("INSERT INTO products (name, price, description, photo, category_product_id, created_at) VALUES (:name, :price, :description, :photo, :category_id, :created_at)");
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':description' => $description,
            ':photo' => $file_name,
            ':category_id' => $category_id,
            ':created_at' => $create_at
        ]);
        return $stmt->rowCount();
    }
    
    return 0;
}

/**
 * Update an existing product
 * 
 * Optionally updates photo if new one is provided
 * 
 * @param int $product_id Product ID to update
 * @param string $name Product name
 * @param float $price Product price
 * @param array|null $photo Uploaded file array from $_FILES (null if not changing)
 * @param string $description Product description
 * @param int $category_id Category ID
 * @return int Number of rows affected
 */
function updateProduct($product_id, $name, $price, $photo, $description, $category_id)
{
    global $db_connection;
    
    if ($photo && $photo['size'] > 0) {
        // Upload new photo
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $photo['name']);
        $tmp_photo = $photo['tmp_name'];
        $uploadPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name;
        
        if (move_uploaded_file($tmp_photo, $uploadPath)) {
            $stmt = $db_connection->prepare("UPDATE products SET name = :name, price = :price, description = :description, photo = :photo, category_product_id = :category_id WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':description' => $description,
                ':photo' => $file_name,
                ':category_id' => $category_id,
                ':id' => $product_id
            ]);
        }
    } else {
        // Update without changing photo
        $stmt = $db_connection->prepare("UPDATE products SET name = :name, price = :price, description = :description, category_product_id = :category_id WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':description' => $description,
            ':category_id' => $category_id,
            ':id' => $product_id
        ]);
    }
    
    return $stmt->rowCount();
}

/**
 * Get all products
 * 
 * @return array Array of product records
 */
function readAllProduct()
{
    global $db_connection;
    $stmt = $db_connection->query("SELECT * FROM products ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get latest products (for homepage)
 * 
 * @param int $limit Number of products to retrieve (default: 4)
 * @return array Array of product records
 */
function latestProducts($limit = 4)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get products by category ID
 * 
 * @param int $category_id Category ID
 * @return array Array of product records
 */
function readAllProductByCategoryId($category_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM products WHERE category_product_id = :category_id ORDER BY id DESC");
    $stmt->execute([':category_id' => $category_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single product by ID
 * 
 * @param int $product_id Product ID
 * @return array|false Product record or false if not found
 */
function getProductById($product_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Delete a product
 * 
 * @param int $id Product ID
 * @return int Number of rows affected
 */
function deleteProduct($id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

/**
 * Search products by name
 * 
 * @param string $search_query Search term
 * @return array Array of matching product records
 */
function searchProduct($search_query)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM products WHERE name LIKE :query");
    $stmt->execute([':query' => '%' . $search_query . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================================
// CATEGORY HELPER FUNCTIONS
// ============================================================================

/**
 * Get category by ID
 * 
 * @param int $category_id Category ID
 * @return array|false Category record or false if not found
 */
function getCategoryById($category_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM category_product WHERE id = :id");
    $stmt->execute([':id' => $category_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ============================================================================
// BLOG CATEGORY FUNCTIONS
// ============================================================================

/**
 * Get all blog categories
 * 
 * @return array Array of blog category records
 */
function readAllBlogCategories()
{
    global $db_connection;
    $stmt = $db_connection->query("SELECT * FROM category_post ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get blog category by ID
 * 
 * @param int $category_id Category ID (0 returns empty result)
 * @return array|false Category record or false if not found
 */
function getCategoryBlogById($category_id)
{
    global $db_connection;
    
    if ($category_id == 0) {
        $stmt = $db_connection->query("SELECT * FROM category_post WHERE id = 0");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    $stmt = $db_connection->prepare("SELECT * FROM category_post WHERE id = :id");
    $stmt->execute([':id' => $category_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Add a new blog category
 * 
 * @param string $name Category name
 * @return int Number of rows affected
 */
function addBlogCategory($name)
{
    global $db_connection;
    $create_at = time();
    $stmt = $db_connection->prepare("INSERT INTO category_post (name, created_at) VALUES (:name, :created_at)");
    $stmt->execute([
        ':name' => $name,
        ':created_at' => $create_at
    ]);
    return $stmt->rowCount();
}

/**
 * Delete a blog category
 * 
 * @param int $id Category ID
 * @return int Number of rows affected
 */
function deleteBlogCategory($id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("DELETE FROM category_post WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

// ============================================================================
// BLOG POST FUNCTIONS
// ============================================================================

/**
 * Get all blog posts
 * 
 * @return array Array of blog post records
 */
function readAllBlogPosts()
{
    global $db_connection;
    $stmt = $db_connection->query("SELECT * FROM posts ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get blog posts by category ID
 * 
 * @param int $category_id Category ID
 * @return array Array of blog post records
 */
function getBlogPostsByCategoryId($category_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM posts WHERE category_id = :category_id ORDER BY id DESC");
    $stmt->execute([':category_id' => $category_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single blog post by ID
 * 
 * @param int $blogPost_id Post ID
 * @return array|false Post record or false if not found
 */
function getBlogPostsById($blogPost_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->execute([':id' => $blogPost_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Add a new blog post
 * 
 * Handles file upload for post photo
 * 
 * @param string $title Post title
 * @param array $photo Uploaded file array from $_FILES
 * @param string $body Post content/body
 * @param int $category_id Category ID
 * @return int Number of rows affected
 */
function addBlogPost($title, $photo, $body, $category_id)
{
    global $db_connection;
    
    $create_at = time();
    $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $photo['name']);
    $tmp_photo = $photo['tmp_name'];
    $uploadPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name;
    
    // Move uploaded file
    if (move_uploaded_file($tmp_photo, $uploadPath)) {
        $stmt = $db_connection->prepare("INSERT INTO posts (title, body, photo, category_id, created_at) VALUES (:title, :body, :photo, :category_id, :created_at)");
        $stmt->execute([
            ':title' => $title,
            ':body' => $body,
            ':photo' => $file_name,
            ':category_id' => $category_id,
            ':created_at' => $create_at
        ]);
        return $stmt->rowCount();
    }
    
    return 0;
}

/**
 * Update an existing blog post
 * 
 * Optionally updates photo if new one is provided
 * 
 * @param int $blogPost_id Post ID to update
 * @param string $title Post title
 * @param array|null $photo Uploaded file array from $_FILES (null if not changing)
 * @param string $body Post content/body
 * @param int $category_id Category ID
 * @return int Number of rows affected
 */
function updateBlogPost($blogPost_id, $title, $photo, $body, $category_id)
{
    global $db_connection;
    
    if ($photo && $photo['size'] > 0) {
        // Upload new photo
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $photo['name']);
        $tmp_photo = $photo['tmp_name'];
        $uploadPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name;
        
        if (move_uploaded_file($tmp_photo, $uploadPath)) {
            $stmt = $db_connection->prepare("UPDATE posts SET title = :title, body = :body, photo = :photo, category_id = :category_id WHERE id = :id");
            $stmt->execute([
                ':title' => $title,
                ':body' => $body,
                ':photo' => $file_name,
                ':category_id' => $category_id,
                ':id' => $blogPost_id
            ]);
        }
    } else {
        // Update without changing photo
        $stmt = $db_connection->prepare("UPDATE posts SET title = :title, body = :body, category_id = :category_id WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':body' => $body,
            ':category_id' => $category_id,
            ':id' => $blogPost_id
        ]);
    }
    
    return $stmt->rowCount();
}

/**
 * Delete a blog post
 * 
 * @param int $id Post ID
 * @return int Number of rows affected
 */
function deleteBlogPosts($id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

// ============================================================================
// CART FUNCTIONS
// ============================================================================

/**
 * Add product to user's cart
 * 
 * @param int $product_id Product ID to add
 * @return int Number of rows affected
 */
function addToCart($product_id)
{
    global $db_connection;
    
    $user = checkIfUserLogin();
    if (!$user) {
        return 0;
    }
    
    $user_id = $user['id'];
    $stmt = $db_connection->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);
    
    return $stmt->rowCount();
}

/**
 * Get user's cart items
 * 
 * @return array Array of cart item records with product details
 */
function readUserCart()
{
    global $db_connection;
    
    $user = checkIfUserLogin();
    if (!$user) {
        return [];
    }
    
    $user_id = $user['id'];
    $stmt = $db_connection->prepare("SELECT c.*, p.name, p.price, p.photo FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Remove item from cart
 * 
 * @param int $id Cart item ID
 * @return int Number of rows affected
 */
function deleteFromCart($id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("DELETE FROM cart WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->rowCount();
}

// ============================================================================
// ORDER FUNCTIONS
// ============================================================================

/**
 * Create a new order from cart items
 * 
 * Clears cart after successful order creation
 * 
 * @param string $phone Customer phone number
 * @param string $userAddress Delivery address
 * @param string $description Order notes/description
 * @return bool True if successful
 */
function addOrder($phone, $userAddress, $description)
{
    global $db_connection;
    
    $user = checkIfUserLogin();
    if (!$user) {
        return false;
    }
    
    $user_id = $user['id'];
    
    // Get cart items
    $stmt = $db_connection->prepare("SELECT * FROM cart WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cartItems)) {
        return false;
    }
    
    // Extract product IDs and calculate total
    $productsCart = [];
    $totalPrice = 0;
    
    foreach ($cartItems as $cart) {
        $productsCart[] = $cart['product_id'];
        // Get product price
        $productStmt = $db_connection->prepare("SELECT price FROM products WHERE id = :id");
        $productStmt->execute([':id' => $cart['product_id']]);
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $totalPrice += $product['price'] * ($cart['quantity'] ?? 1);
        }
    }
    
    $products = serialize($productsCart);
    
    // Clear cart
    $db_connection->prepare("DELETE FROM cart WHERE user_id = :user_id")->execute([':user_id' => $user_id]);
    
    // Create order
    $stmt = $db_connection->prepare("INSERT INTO orders (user_id, products, status, user_address, description, phone, total_price, created_at) VALUES (:user_id, :products, :status, :user_address, :description, :phone, :total_price, :created_at)");
    $stmt->execute([
        ':user_id' => $user_id,
        ':products' => $products,
        ':status' => 0,
        ':user_address' => $userAddress,
        ':description' => $description,
        ':phone' => $phone,
        ':total_price' => $totalPrice,
        ':created_at' => time()
    ]);
    
    return true;
}

/**
 * Get all orders for current user
 * 
 * @return array Array of order records
 */
function readAllUserOrders()
{
    global $db_connection;
    
    $user = checkIfUserLogin();
    if (!$user) {
        return [];
    }
    
    $user_id = $user['id'];
    $stmt = $db_connection->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single order by ID
 * 
 * @param int $order_id Order ID
 * @return array|false Order record or false if not found
 */
function getUserOrderById($order_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM orders WHERE id = :id");
    $stmt->execute([':id' => $order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all orders (admin function)
 * 
 * @return array Array of all order records
 */
function readAllOrders()
{
    global $db_connection;
    $stmt = $db_connection->query("SELECT * FROM orders ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get orders by status (admin function)
 * 
 * @param int $status Order status (0=pending, 1=confirmed, etc.)
 * @return array Array of order records
 */
function readAllOrdersByStatus($status)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM orders WHERE status = :status ORDER BY id DESC");
    $stmt->execute([':status' => $status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update order status (admin function)
 * 
 * @param int $order_id Order ID
 * @param int $status New status
 * @return int Number of rows affected
 */
function updateOrdersByStatus($order_id, $status)
{
    global $db_connection;
    $stmt = $db_connection->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $status,
        ':id' => $order_id
    ]);
    return $stmt->rowCount();
}

/**
 * Get all orders by user ID (admin function)
 * 
 * @param int $user_id User ID
 * @return array Array of order records
 */
function readAllUserOrdersById($user_id)
{
    global $db_connection;
    $stmt = $db_connection->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all users (admin function)
 * 
 * @return array Array of user records
 */
function readAllUsers()
{
    global $db_connection;
    $stmt = $db_connection->query("SELECT * FROM users ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}