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

-- Insert sample categories for products
INSERT OR IGNORE INTO category_product (name, photo, created_at) VALUES 
('الکترونیک', 'electronics.jpg', strftime('%s', 'now')),
('پوشاک', 'clothing.jpg', strftime('%s', 'now')),
('کتاب', 'books.jpg', strftime('%s', 'now')),
('ورزشی', 'sports.jpg', strftime('%s', 'now')),
('خانه و آشپزخانه', 'home.jpg', strftime('%s', 'now')),
('زیبایی و سلامت', 'beauty.jpg', strftime('%s', 'now'));

-- Insert sample blog categories
INSERT OR IGNORE INTO category_post (name, created_at) VALUES 
('اخبار فناوری', strftime('%s', 'now')),
('آموزش', strftime('%s', 'now')),
('بررسی محصولات', strftime('%s', 'now'));

-- Insert sample products
INSERT OR IGNORE INTO products (name, description, price, photo, category_product_id, stock, created_at) VALUES
('گوشی موبایل آیفون ۱۳', 'گوشی موبایل اپل مدل آیفون ۱۳ با حافظه ۱۲۸ گیگابایت', 35000000, 'iphone13.jpg', 1, 10, strftime('%s', 'now')),
('لپ‌تاپ مک‌بوک پرو', 'لپ‌تاپ اپل مدل مک‌بوک پرو ۲۰۲۲ با پردازنده M2', 65000000, 'macbook.jpg', 1, 5, strftime('%s', 'now')),
('هدفون بی‌سیم سونی', 'هدفون بی‌سیم سونی مدل WH-1000XM5 با نویز کنسلینگ', 12000000, 'headphone.jpg', 1, 20, strftime('%s', 'now')),
('ساعت هوشمند اپل', 'ساعت هوشمند اپل واچ سری ۸ با قابلیت اندازه‌گیری اکسیژن خون', 15000000, 'applewatch.jpg', 1, 15, strftime('%s', 'now')),
('تی‌شرت مردانه', 'تی‌شرت مردانه نخی با طرح مدرن', 350000, 'tshirt.jpg', 2, 50, strftime('%s', 'now')),
('شلوار جین', 'شلوار جین مردانه راسته کلاسیک', 890000, 'jeans.jpg', 2, 30, strftime('%s', 'now')),
('کفش ورزشی نایک', 'کفش ورزشی نایک مخصوص دویدن با کفی راحت', 2500000, 'nike_shoes.jpg', 2, 25, strftime('%s', 'now')),
('کتاب تمیزترین کد', 'کتاب تمیزترین کد نوشته رابرت سی مارتین - آموزش اصول کدنویسی حرفه‌ای', 450000, 'clean_code.jpg', 3, 40, strftime('%s', 'now')),
('کتاب الگوریتم‌ها', 'کتاب مقدمه‌ای بر الگوریتم‌ها نوشته توماس کورمن', 680000, 'algorithms.jpg', 3, 35, strftime('%s', 'now')),
('توپ فوتبال', 'توپ فوتبال حرفه‌ای سایز ۵ مناسب برای مسابقات', 450000, 'football.jpg', 4, 60, strftime('%s', 'now')),
('راکت تنیس', 'راکت تنیس حرفه‌ای ویلسون با روکش کربن', 3200000, 'tennis_racket.jpg', 4, 12, strftime('%s', 'now')),
('قهوه‌ساز برقی', 'قهوه‌ساز برقی دلونگی با قابلیت اسپرسو و کاپوچینو', 8500000, 'coffee_maker.jpg', 5, 18, strftime('%s', 'now')),
('مخلوط کن', 'مخلوط کن فیلیپس با قدرت ۱۰۰۰ وات و ظرفیت ۲ لیتر', 2800000, 'blender.jpg', 5, 22, strftime('%s', 'now')),
('کرم مرطوب کننده', 'کرم مرطوب کننده پوست صورت با SPF 30', 280000, 'moisturizer.jpg', 6, 45, strftime('%s', 'now')),
('شامپو طبیعی', 'شامپو طبیعی بدون سولفات با عصاره آرگان', 195000, 'shampoo.jpg', 6, 70, strftime('%s', 'now'));

-- Insert sample blog posts
INSERT OR IGNORE INTO posts (title, body, photo, category_id, created_at) VALUES
('بررسی کامل آیفون ۱۳ پرو مکس', 'آیفون ۱۳ پرو مکس جدیدترین پرچمدار اپل است که با ویژگی‌های منحصر به فردی وارد بازار شده است. در این مقاله به بررسی کامل این گوشی می‌پردازیم...\n\n## طراحی و ساخت\nطراحی آیفون ۱۳ پرو مکس مشابه نسل قبل است اما با حاشیه‌های کمتر و ناچ کوچکتر...\n\n## دوربین\nسیستم دوربین سه گانه با سنسورهای بهبود یافته...', 'iphone_review.jpg', 1, strftime('%s', 'now')),
('آموزش PHP مقدماتی تا پیشرفته', 'در این سری مقالات قصد داریم PHP را از صفر تا صد آموزش دهیم. PHP یکی از محبوب‌ترین زبان‌های برنامه‌نویسی سمت سرور است...\n\n## فصل اول: مقدمه‌ای بر PHP\nPHP چیست و چرا باید آن را یاد بگیریم؟\n\n## نصب و راه‌اندازی\n...', 'php_tutorial.jpg', 2, strftime('%s', 'now')),
('مقایسه لپ‌تاپ‌های ۲۰۲۴', 'در این مقاله بهترین لپ‌تاپ‌های سال ۲۰۲۴ را در رده‌های قیمتی مختلف مقایسه کرده‌ایم...\n\n## لپ‌تاپ‌های اقتصادی\n...\n\n## لپ‌تاپ‌های میان‌رده\n...\n\n## لپ‌تاپ‌های حرفه‌ای\n...', 'laptop_comparison.jpg', 3, strftime('%s', 'now')),
('راهنمای خرید گوشی موبایل', 'قبل از خرید گوشی موبایل باید به چه نکاتی توجه کنیم؟ در این راهنما تمام معیارهای مهم را بررسی می‌کنیم...\n\n## بودجه\nاولین قدم تعیین بودجه است...\n\n## نیازها\n...', 'mobile_guide.jpg', 3, strftime('%s', 'now')));

-- Insert a test regular user (password: user123)
INSERT OR IGNORE INTO users (name, last_name, email, password, role, created_at) 
VALUES ('کاربر', 'تستی', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, strftime('%s', 'now'));
