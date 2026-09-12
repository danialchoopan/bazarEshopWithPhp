<?php
/**
 * Database Seeder
 * 
 * Seeds the database with sample data for development and testing.
 * Controlled by SEED_DATA environment variable in .env file.
 * 
 * @package BazarShop\Database
 */

class DatabaseSeeder
{
    /**
     * Database connection
     */
    private $db;

    /**
     * Constructor
     * 
     * @param mixed $db Database connection instance
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Run all seeders
     */
    public function run(): void
    {
        echo "  → Seeding categories...\n";
        $this->seedCategories();
        
        echo "  → Seeding products...\n";
        $this->seedProducts();
        
        echo "  → Seeding blog categories...\n";
        $this->seedBlogCategories();
        
        echo "  → Seeding blog posts...\n";
        $this->seedBlogPosts();
        
        echo "  → Seeding users...\n";
        $this->seedUsers();
        
        echo "  → Seeding coupons...\n";
        $this->seedCoupons();
    }

    /**
     * Seed product categories
     */
    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'الکترونیک', 'slug' => 'electronics', 'description' => 'گوشی، لپ‌تاپ و لوازم الکترونیکی'],
            ['name' => 'پوشاک', 'slug' => 'clothing', 'description' => 'لباس مردانه، زنانه و بچگانه'],
            ['name' => 'کتاب', 'slug' => 'books', 'description' => 'کتاب‌های فارسی و انگلیسی'],
            ['name' => 'ورزش و سفر', 'slug' => 'sports', 'description' => 'لوازم ورزشی و کمپینگ'],
            ['name' => 'خانه و آشپزخانه', 'slug' => 'home', 'description' => 'لوازم خانگی و دکوری'],
            ['name' => 'زیبایی و سلامت', 'slug' => 'beauty', 'description' => 'لوازم آرایشی و بهداشتی'],
        ];

        foreach ($categories as $category) {
            $stmt = $this->db->getConnection()->prepare(
                "INSERT OR IGNORE INTO category_product (name, slug, description) VALUES (?, ?, ?)"
            );
            $stmt->execute([$category['name'], $category['slug'], $category['description']]);
        }
    }

    /**
     * Seed products with realistic data
     */
    private function seedProducts(): void
    {
        $products = [
            [
                'title' => 'گوشی موبایل آیفون ۱۳ پرو',
                'slug' => 'iphone-13-pro',
                'description' => 'گوشی هوشمند اپل با دوربین ۱۲ مگاپیکسلی و پردازنده A15 Bionic',
                'price' => 45000000,
                'category_id' => 1,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/400x400?text=iPhone+13+Pro'
            ],
            [
                'title' => 'لپ‌تاپ مک‌بوک پرو ۱۴ اینچ',
                'slug' => 'macbook-pro-14',
                'description' => 'لپ‌تاپ حرفه‌ای اپل با چیپ M1 Pro و نمایشگر Liquid Retina XDR',
                'price' => 85000000,
                'category_id' => 1,
                'stock' => 15,
                'image' => 'https://via.placeholder.com/400x400?text=MacBook+Pro'
            ],
            [
                'title' => 'هدفون بی‌سیم سونی WH-1000XM5',
                'slug' => 'sony-wh1000xm5',
                'description' => 'هدفون نویز کنسلینگ حرفه‌ای با کیفیت صوتی عالی',
                'price' => 12000000,
                'category_id' => 1,
                'stock' => 40,
                'image' => 'https://via.placeholder.com/400x400?text=Sony+Headphones'
            ],
            [
                'title' => 'تی‌شرت مردانه نخی',
                'slug' => 'mens-cotton-tshirt',
                'description' => 'تی‌شرت راحت و خنک از جنس نخ پنبه‌ای',
                'price' => 350000,
                'category_id' => 2,
                'stock' => 100,
                'image' => 'https://via.placeholder.com/400x400?text=T-Shirt'
            ],
            [
                'title' => 'شلوار جین زنانه',
                'slug' => 'womens-jeans',
                'description' => 'شلوار جین با کیفیت و طراحی مدرن',
                'price' => 890000,
                'category_id' => 2,
                'stock' => 60,
                'image' => 'https://via.placeholder.com/400x400?text=Jeans'
            ],
            [
                'title' => 'کفش ورزشی نایکی',
                'slug' => 'nike-sport-shoes',
                'description' => 'کفش راحت و سبک برای دویدن و ورزش',
                'price' => 2500000,
                'category_id' => 4,
                'stock' => 45,
                'image' => 'https://via.placeholder.com/400x400?text=Nike+Shoes'
            ],
            [
                'title' => 'کتاب شازده کوچولو',
                'slug' => 'little-prince',
                'description' => 'رمان معروف آنتوان دو سنت اگزوپری',
                'price' => 120000,
                'category_id' => 3,
                'stock' => 200,
                'image' => 'https://via.placeholder.com/400x400?text=Little+Prince'
            ],
            [
                'title' => 'ساعت هوشمند اپل واچ سری ۸',
                'slug' => 'apple-watch-series-8',
                'description' => 'ساعت هوشمند با سنسورهای پیشرفته سلامت',
                'price' => 18000000,
                'category_id' => 1,
                'stock' => 30,
                'image' => 'https://via.placeholder.com/400x400?text=Apple+Watch'
            ],
            [
                'title' => 'قهوه‌ساز دلونگی',
                'slug' => 'delonghi-coffee-maker',
                'description' => 'دستگاه اسپرسوساز حرفه‌ای ایتالیایی',
                'price' => 8500000,
                'category_id' => 5,
                'stock' => 20,
                'image' => 'https://via.placeholder.com/400x400?text=Coffee+Maker'
            ],
            [
                'title' => 'چراغ خواب LED',
                'slug' => 'led-night-lamp',
                'description' => 'چراغ خواب با قابلیت تغییر رنگ و کنترل از راه دور',
                'price' => 450000,
                'category_id' => 5,
                'stock' => 80,
                'image' => 'https://via.placeholder.com/400x400?text=LED+Lamp'
            ],
            [
                'title' => 'مات یوگا',
                'slug' => 'yoga-mat',
                'description' => 'مت ورزشی ضد لغزش برای یوگا و پیلاتس',
                'price' => 680000,
                'category_id' => 4,
                'stock' => 55,
                'image' => 'https://via.placeholder.com/400x400?text=Yoga+Mat'
            ],
            [
                'title' => 'کرم مرطوب کننده',
                'slug' => 'moisturizing-cream',
                'description' => 'کرم آبرسان پوست صورت و بدن',
                'price' => 320000,
                'category_id' => 6,
                'stock' => 90,
                'image' => 'https://via.placeholder.com/400x400?text=Moisturizer'
            ],
            [
                'title' => 'کوله پشتی مسافرتی',
                'slug' => 'travel-backpack',
                'description' => 'کوله بزرگ و مقاوم برای سفر و کوهنوردی',
                'price' => 1200000,
                'category_id' => 4,
                'stock' => 35,
                'image' => 'https://via.placeholder.com/400x400?text=Backpack'
            ],
            [
                'title' => 'عطر دیور ساواج',
                'slug' => 'dior-savage',
                'description' => 'عطر مردانه با رایحه تلخ و گرم',
                'price' => 4500000,
                'category_id' => 6,
                'stock' => 25,
                'image' => 'https://via.placeholder.com/400x400?text=Perfume'
            ],
            [
                'title' => 'بلندگو بلوتوثی JBL',
                'slug' => 'jbl-bluetooth-speaker',
                'description' => 'اسپیکر قابل حمل با باتری ۱۲ ساعته',
                'price' => 3200000,
                'category_id' => 1,
                'stock' => 50,
                'image' => 'https://via.placeholder.com/400x400?text=JBL+Speaker'
            ],
        ];

        foreach ($products as $product) {
            $stmt = $this->db->getConnection()->prepare(
                "INSERT OR IGNORE INTO products (title, slug, description, price, category_id, stock, image) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $product['title'],
                $product['slug'],
                $product['description'],
                $product['price'],
                $product['category_id'],
                $product['stock'],
                $product['image']
            ]);
        }
    }

    /**
     * Seed blog categories
     */
    private function seedBlogCategories(): void
    {
        $categories = [
            ['name' => 'اخبار فناوری', 'slug' => 'tech-news'],
            ['name' => 'آموزش خرید', 'slug' => 'buying-guide'],
            ['name' => 'بررسی محصولات', 'slug' => 'product-reviews'],
        ];

        foreach ($categories as $category) {
            $stmt = $this->db->getConnection()->prepare(
                "INSERT OR IGNORE INTO category_post (name, slug) VALUES (?, ?)"
            );
            $stmt->execute([$category['name'], $category['slug']]);
        }
    }

    /**
     * Seed blog posts
     */
    private function seedBlogPosts(): void
    {
        $posts = [
            [
                'title' => 'راهنمای خرید گوشی موبایل در سال ۱۴۰۳',
                'slug' => 'mobile-buying-guide-1403',
                'content' => 'در این مقاله به بررسی مهم‌ترین نکات هنگام خرید گوشی موبایل می‌پردازیم...',
                'excerpt' => 'نکات کلیدی برای انتخاب بهترین گوشی موبایل',
                'category_id' => 2,
                'author_id' => 1,
            ],
            [
                'title' => 'بررسی آیفون ۱۳ پرو - آیا ارزش خرید دارد؟',
                'slug' => 'iphone-13-pro-review',
                'content' => 'آیفون ۱۳ پرو با ویژگی‌های جدید خود یکی از بهترین گوشی‌های بازار است...',
                'excerpt' => 'بررسی کامل پرچمدار اپل',
                'category_id' => 3,
                'author_id' => 1,
            ],
            [
                'title' => 'تأثیر هوش مصنوعی بر تجارت الکترونیک',
                'slug' => 'ai-impact-on-ecommerce',
                'content' => 'هوش مصنوعی در حال متحول کردن صنعت فروش آنلاین است...',
                'excerpt' => 'کاربردهای AI در فروشگاه‌های اینترنتی',
                'category_id' => 1,
                'author_id' => 2,
            ],
            [
                'title' => '۱۰ نکته برای افزایش امنیت حساب کاربری',
                'slug' => 'account-security-tips',
                'content' => 'امنیت حساب‌های کاربری در دنیای دیجیتال بسیار مهم است...',
                'excerpt' => 'راهکارهای محافظت از اطلاعات شخصی',
                'category_id' => 1,
                'author_id' => 1,
            ],
        ];

        foreach ($posts as $post) {
            $stmt = $this->db->getConnection()->prepare(
                "INSERT OR IGNORE INTO posts (title, slug, content, excerpt, category_id, author_id) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $post['title'],
                $post['slug'],
                $post['content'],
                $post['excerpt'],
                $post['category_id'],
                $post['author_id']
            ]);
        }
    }

    /**
     * Seed users (admin and regular user)
     */
    private function seedUsers(): void
    {
        // Admin user
        $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $this->db->getConnection()->prepare(
            "INSERT OR IGNORE INTO users (name, email, password, role, phone, address) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            'مدیر سایت',
            'admin@bazar.local',
            $adminPassword,
            'admin',
            '09123456789',
            'تهران، خیابان ولیعصر'
        ]);

        // Regular user
        $userPassword = password_hash('user123', PASSWORD_BCRYPT);
        $stmt = $this->db->getConnection()->prepare(
            "INSERT OR IGNORE INTO users (name, email, password, role, phone, address) 
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            'علی محمدی',
            'user@example.com',
            $userPassword,
            'customer',
            '09129876543',
            'اصفهان، میدان نقش جهان'
        ]);
    }

    /**
     * Seed sample coupons
     */
    private function seedCoupons(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME20',
                'description' => 'کد تخفیف خوش‌آمدگویی',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'min_order_amount' => 500000,
                'max_discount_amount' => 200000,
                'usage_limit' => 100,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
            ],
            [
                'code' => 'SUMMER1403',
                'description' => 'تخفیف ویژه تابستان',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'min_order_amount' => 1000000,
                'max_discount_amount' => 500000,
                'usage_limit' => 500,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+60 days')),
            ],
            [
                'code' => 'FREESHIP',
                'description' => 'ارسال رایگان',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'min_order_amount' => 300000,
                'max_discount_amount' => 50000,
                'usage_limit' => 1000,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+90 days')),
            ],
            [
                'code' => 'TECH500K',
                'description' => 'تخفیف محصولات الکترونیکی',
                'discount_type' => 'fixed',
                'discount_value' => 500000,
                'min_order_amount' => 5000000,
                'max_discount_amount' => 500000,
                'usage_limit' => 50,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+45 days')),
            ],
        ];

        foreach ($coupons as $coupon) {
            $stmt = $this->db->getConnection()->prepare(
                "INSERT OR IGNORE INTO coupons 
                 (code, description, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, expires_at, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $coupon['code'],
                $coupon['description'],
                $coupon['discount_type'],
                $coupon['discount_value'],
                $coupon['min_order_amount'],
                $coupon['max_discount_amount'],
                $coupon['usage_limit'],
                $coupon['expires_at'],
                1
            ]);
        }
    }
}
