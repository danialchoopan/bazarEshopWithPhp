-- SQLite Database Schema for Bazar Shop
-- Easy setup: Just copy this file and run with sqlite3

-- Enable foreign keys
PRAGMA foreign_keys = ON;

-- Category Product Table
CREATE TABLE IF NOT EXISTS category_product (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    photo TEXT NOT NULL DEFAULT '',
    created_at INTEGER NOT NULL DEFAULT (strftime('%s', 'now'))
);

-- Category Post Table
CREATE TABLE IF NOT EXISTS category_post (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    created_at INTEGER NOT NULL DEFAULT (strftime('%s', 'now'))
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    last_name TEXT,
    phone TEXT,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role INTEGER DEFAULT 0,
    created_at INTEGER NOT NULL DEFAULT (strftime('%s', 'now'))
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL DEFAULT 0,
    photo TEXT,
    category_id INTEGER DEFAULT 0,
    stock INTEGER DEFAULT 0,
    created_at INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
    FOREIGN KEY (category_id) REFERENCES category_product(id)
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    products TEXT NOT NULL,
    status INTEGER NOT NULL DEFAULT 0,
    user_address TEXT,
    description TEXT,
    phone TEXT,
    total_price REAL DEFAULT 0,
    created_at INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Posts Table (Blog)
CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    body TEXT,
    photo TEXT,
    category_id INTEGER DEFAULT 0,
    created_at INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
    FOREIGN KEY (category_id) REFERENCES category_post(id)
);

-- Order Items Table (for better order management)
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1,
    price REAL NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_created ON products(created_at);
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_cart_user ON cart(user_id);
CREATE INDEX IF NOT EXISTS idx_posts_category ON posts(category_id);

-- Insert default admin user (password: admin123 - hashed with bcrypt)
-- Note: In production, always use proper password hashing
INSERT OR IGNORE INTO users (name, last_name, email, password, role, created_at) 
VALUES ('Admin', 'User', 'admin@bazar.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, strftime('%s', 'now'));

-- Insert sample categories
INSERT OR IGNORE INTO category_product (name, photo, created_at) VALUES 
('الکترونیک', 'electronics.jpg', strftime('%s', 'now')),
('پوشاک', 'clothing.jpg', strftime('%s', 'now')),
('کتاب', 'books.jpg', strftime('%s', 'now')),
('ورزشی', 'sports.jpg', strftime('%s', 'now'));

-- Insert sample blog category
INSERT OR IGNORE INTO category_post (name, created_at) VALUES 
('اخبار فناوری', strftime('%s', 'now')),
('آموزش', strftime('%s', 'now'));
