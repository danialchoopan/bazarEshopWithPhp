# بازار - فروشگاه آنلاین

فروشگاه آنلاین ساخته شده با PHP خالص (Vanilla PHP)

> این پروژه یکی از اولین کارهای من با PHP بود. بعد از چند سال تصمیم گرفتم دوباره بهش سر بزنم و با ساختار مدرن بازنویسیش کنم — از MVC و prepared statements گرفته تا داشبورد مدیریت و UI بهتر.

## 🚀 شروع سریع

### نصب در ۳۰ ثانیه با SQLite (بدون نیاز به MySQL!)

```bash
# کپی کردن فایل‌ها
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp

# اجرای اسکریپت نصب خودکار
chmod +x setup.sh
./setup.sh
# گزینه 2 (SQLite) را انتخاب کنید
```

تمام! حالا به آدرس `http://localhost/bazarEshopWithPhp/public/` بروید.

**ورود ادمین:** 
- ایمیل: `admin@bazar.local`
- رمز: `admin123`

---

## امکانات

### فروشگاه
- نمایش محصولات با دسته‌بندی و فیلتر
- جستجوی محصولات
- سبد خرید با مدیریت تعداد
- فرآیند تکمیل خرید
- پیگیری سفارشات

### پنل مدیریت
- داشبورد با نمودارهای آماری (Chart.js)
- مدیریت محصولات (افزودن، ویرایش، حذف)
- مدیریت موجودی انبار
- مدیریت سفارشات با چرخه کامل وضعیت
- مدیریت کاربران و نقش‌ها
- مدیریت بلاگ با ویرایشگر متن غنی (CKEditor)
- دسته‌بندی محصولات و پست‌ها

### امنیت
- عبارات آماده (Prepared Statements) برای تمام کوئری‌های دیتابیس
- رمزگذاری رمز عبور با bcrypt
- محافظت CSRF در تمام فرم‌ها
- اعتبارسنجی ورودی در سمت سرور
- جلوگیری از XSS
- آپلود امن فایل‌ها

### UI/UX
- طراحی واکنش‌گرا (Mobile Responsive)
- ناوبری موبایل با نوار پایین
- اعلان‌های Toast با بسته شدن خودکار
- دکمه بازگشت به بالا
- پشتیبانی RTL کامل
- **انیمیشن‌های مدرن و gradientهای زیبا**
- **Dark Mode Ready**

### دیتابیس
- **پشتیبانی از SQLite** (راه‌اندازی آسان، بدون نیاز به سرور)
- پشتیبانی از MySQL/MariaDB
- **کانفیگ آسان با فایل .env**

## تکنولوژی‌ها

- **Backend:** PHP 8.0+ (Vanilla PHP)
- **Database:** MySQL / MariaDB **یا SQLite**
- **Frontend:** Bootstrap 5 RTL, Bootstrap Icons
- **Charts:** Chart.js
- **Editor:** CKEditor 5
- **Font:** Vazir

## پیش‌نیازها

### برای SQLite (توصیه می‌شود):
- PHP 8.0+
- فعال بودن افزونه PDO_SQLite

### برای MySQL:
- PHP 8.0+
- MySQL 5.7+ یا MariaDB 10.3+
- فعال بودن افزونه PDO_MySQL

### عمومی:
- قابلیت URL Rewrite (Apache mod_rewrite یا Nginx)

## نصب گام به گام

### روش ۱: استفاده از اسکریپت نصب (توصیه می‌شود)

```bash
chmod +x setup.sh
./setup.sh
```

اسکریپت به صورت خودکار:
- فایل `.env` را می‌سازد
- از شما می‌پرسد MySQL می‌خواهید یا SQLite
- دیتابیس را ایجاد و جداول را ایمپورت می‌کند

### روش ۲: نصب دستی

#### ۱. کلون کردن پروژه
```bash
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp
```

#### ۲. تنظیم محیط
```bash
cp .env.example .env
```

فایل `.env` را ویرایش کنید:

**برای SQLite:**
```env
DB_DRIVER=sqlite
DB_DATABASE=database/sqlite.db
```

**برای MySQL:**
```env
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=em-bazar-shop-db
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### ۳. ایجاد دیتابیس

**SQLite:**
```bash
sqlite3 database/sqlite.db < database/sqlite/schema.sql
```

**MySQL:**
```bash
mysql -u root -p -e "CREATE DATABASE \`em-bazar-shop-db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p em-bazar-shop-db < em-bazar-shop-db.sql
mysql -u root -p em-bazar-shop-db < database/migrations/001_add_modernization_tables.sql
```

#### ۴. تنظیم Web Server
   - Document Root را روی پوشه `public/` تنظیم کنید
   - مطمئن شوید mod_rewrite فعال است (برای Apache)

#### ۵. ورود به پنل مدیریت
   - آدرس: `http://your-domain/admin`
   - ایمیل: `admin@bazar.local`
   - رمز: `admin123`

## ساختار پروژه

```
├── app/                    # کد اصلی اپلیکیشن
│   ├── Config/             # پیکربندی و مسیریاب
│   ├── Controllers/        # کنترلرها
│   │   └── Admin/          # کنترلرهای پنل مدیریت
│   ├── Database/           # اتصال دیتابیس
│   ├── Helpers/            # ابزارهای کمکی
│   ├── Middleware/          # میان‌افزارها (احراز هویت، CSRF)
│   └── Models/             # مدل‌ها
├── database/
│   ├── sqlite/             # اسکیمای SQLite
│   │   └── schema.sql
│   └── migrations/         # فایل‌های مایگریشن MySQL
├── public/                 # روت وب (public_html)
│   ├── css/                # فایل‌های استایل
│   ├── js/                 # فایل‌های جاوااسکریپت
│   └── uploads/            # فایل‌های آپلود شده
├── resources/
│   └── views/              # قالب‌های نمایش
│       ├── layouts/        # قالب‌های اصلی
│       ├── admin/          # قالب‌های پنل مدیریت
│       └── ...             # قالب‌های بخش‌های مختلف
├── .env.example            # نمونه فایل تنظیمات
├── setup.sh                # اسکریپت نصب خودکار
└── em-bazar-shop-db.sql    # فایل دیتابیس MySQL
```

## مجوز

MIT License

---

# Bazar - Online Store

An online store built with vanilla PHP

> This was one of my first PHP projects. After a few years I decided to revisit it and rewrite it with a modern architecture — from MVC and prepared statements to an admin dashboard and improved UI.

## 🚀 Quick Start

### Setup in 30 seconds with SQLite (No MySQL needed!)

```bash
# Clone the project
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp

# Run the auto-setup script
chmod +x setup.sh
./setup.sh
# Choose option 2 (SQLite)
```

That's it! Visit `http://localhost/bazarEshopWithPhp/public/`

**Admin Login:**
- Email: `admin@bazar.local`
- Password: `admin123`

---

## Features

### Shop
- Product listing with category filtering
- Product search
- Cart with quantity management
- Checkout flow
- Order tracking

### Admin Dashboard
- Dashboard with statistical charts (Chart.js)
- Product CRUD management
- Inventory management
- Full order lifecycle management
- User and role management
- Blog CMS with rich text editor (CKEditor)
- Product and post categories

### Security
- Prepared statements for all database queries
- Password hashing with bcrypt
- CSRF protection on all forms
- Server-side input validation
- XSS prevention
- Secure file uploads

### UI/UX
- Fully responsive design (Mobile)
- Mobile bottom navigation bar
- Auto-dismissing toast notifications
- Scroll-to-top button
- Full RTL support
- **Modern animations and beautiful gradients**
- **Dark Mode Ready**

### Database
- **SQLite support** (easy setup, no server needed)
- MySQL/MariaDB support
- **Easy configuration with .env file**

## Tech Stack

- **Backend:** PHP 8.0+ (Vanilla PHP)
- **Database:** MySQL / MariaDB **or SQLite**
- **Frontend:** Bootstrap 5 RTL, Bootstrap Icons
- **Charts:** Chart.js
- **Editor:** CKEditor 5
- **Font:** Vazir

## Prerequisites

### For SQLite (Recommended):
- PHP 8.0+
- PDO_SQLite extension enabled

### For MySQL:
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- PDO_MySQL extension enabled

### General:
- URL Rewrite support (Apache mod_rewrite or Nginx)

## Installation

### Method 1: Using Setup Script (Recommended)

```bash
chmod +x setup.sh
./setup.sh
```

The script will:
- Create `.env` file automatically
- Ask if you want MySQL or SQLite
- Create database and import tables

### Method 2: Manual Installation

#### 1. Clone the project
```bash
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp
```

#### 2. Setup environment
```bash
cp .env.example .env
```

Edit `.env` file:

**For SQLite:**
```env
DB_DRIVER=sqlite
DB_DATABASE=database/sqlite.db
```

**For MySQL:**
```env
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=em-bazar-shop-db
DB_USERNAME=root
DB_PASSWORD=your_password
```

#### 3. Create database

**SQLite:**
```bash
sqlite3 database/sqlite.db < database/sqlite/schema.sql
```

**MySQL:**
```bash
mysql -u root -p -e "CREATE DATABASE \`em-bazar-shop-db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p em-bazar-shop-db < em-bazar-shop-db.sql
mysql -u root -p em-bazar-shop-db < database/migrations/001_add_modernization_tables.sql
```

#### 4. Configure Web Server
   - Set Document Root to `public/`
   - Make sure mod_rewrite is enabled (for Apache)

#### 5. Access Admin Panel
   - URL: `http://your-domain/admin`
   - Email: `admin@bazar.local`
   - Password: `admin123`

## Project Structure

```
├── app/                    # Core application code
│   ├── Config/             # Config and router
│   ├── Controllers/        # Controllers
│   │   └── Admin/          # Admin controllers
│   ├── Database/           # Database connection
│   ├── Helpers/            # Helper utilities
│   ├── Middleware/          # Middleware (auth, CSRF)
│   └── Models/             # Models
├── database/
│   ├── sqlite/             # SQLite schema
│   │   └── schema.sql
│   └── migrations/         # MySQL migration files
├── public/                 # Web root
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript
│   └── uploads/            # Uploaded files
├── resources/
│   └── views/              # View templates
│       ├── layouts/        # Layout templates
│       ├── admin/          # Admin templates
│       └── ...             # Feature templates
├── .env.example            # Environment config template
├── setup.sh                # Auto-setup script
└── em-bazar-shop-db.sql    # MySQL database dump
```

## License

MIT License
