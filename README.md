# بازار - فروشگاه آنلاین

فروشگاه آنلاین ساخته شده با PHP خالص (Vanilla PHP) با پشتیبانی از زبان فارسی و راست‌چین (RTL).

> این پروژه یکی از اولین کارهای من با PHP بود. بعد از چند سال تصمیم گرفتم دوباره بهش سر بزنم و با ساختار مدرن بازنویسیش کنم — از MVC و prepared statements گرفته تا داشبورد مدیریت و UI بهتر.

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

## تکنولوژی‌ها

- **Backend:** PHP 8.0+ (Vanilla PHP)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5 RTL, Bootstrap Icons
- **Charts:** Chart.js
- **Editor:** CKEditor 5
- **Font:** Vazir

## پیش‌نیازها

- PHP 8.0 یا بالاتر
- MySQL 5.7+ یا MariaDB 10.3+
- قابلیت URL Rewrite (Apache mod_rewrite یا Nginx)

## نصب

1. **کلون کردن پروژه**
```bash
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp
```

2. **تنظیم محیط**
```bash
cp .env.example .env
```
فایل `.env` را ویرایش کنید و اطلاعات دیتابیس را وارد کنید.

3. **ایجاد دیتابیس و جداول**
```bash
mysql -u root -p -e "CREATE DATABASE `em-bazar-shop-db`"
mysql -u root -p em-bazar-shop-db < em-bazar-shop-db.sql
mysql -u root -p em-bazar-shop-db < database/migrations/001_add_modernization_tables.sql
```

4. **تنظیم Web Server**
   - Document Root را روی پوشه `public/` تنظیم کنید
   - مطمئن شوید mod_rewrite فعال است (برای Apache)

5. **ورود به پنل مدیریت**
   - آدرس: `http://your-domain/admin`
   - ایمیل و رمز عبور کاربر ادمین موجود در دیتابیس را استفاده کنید

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
│   └── migrations/         # فایل‌های مایگریشن
├── public/                 # روت وب (public_html)
│   ├── css/                # فایل‌های استایل
│   ├── js/                 # فایل‌های جاوااسکریپت
│   └── uploads/            # فایل‌های آپلود شده
├── resources/
│   └── views/              # قالب‌های نمایش
│       ├── layouts/        # قالب‌های اصلی
│       ├── admin/          # قالب‌های پنل مدیریت
│       └── ...             # قالب‌های بخش‌های مختلف
└── em-bazar-shop-db.sql    # فایل دیتابیس اولیه
```

## مجوز

MIT License

---

# Bazar - Online Store

An online store built with vanilla PHP with full Persian/Farsi RTL support.

> This was one of my first PHP projects. After a few years I decided to revisit it and rewrite it with a modern architecture — from MVC and prepared statements to an admin dashboard and improved UI.

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

## Tech Stack

- **Backend:** PHP 8.0+ (Vanilla PHP)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5 RTL, Bootstrap Icons
- **Charts:** Chart.js
- **Editor:** CKEditor 5
- **Font:** Vazir

## Prerequisites

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- URL Rewrite support (Apache mod_rewrite or Nginx)

## Installation

1. **Clone the project**
```bash
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp
```

2. **Setup environment**
```bash
cp .env.example .env
```
Edit `.env` and fill in your database credentials.

3. **Create database and tables**
```bash
mysql -u root -p -e "CREATE DATABASE `em-bazar-shop-db`"
mysql -u root -p em-bazar-shop-db < em-bazar-shop-db.sql
mysql -u root -p em-bazar-shop-db < database/migrations/001_add_modernization_tables.sql
```

4. **Configure Web Server**
   - Set Document Root to `public/`
   - Make sure mod_rewrite is enabled (for Apache)

5. **Access Admin Panel**
   - URL: `http://your-domain/admin`
   - Use the admin credentials from the database

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
│   └── migrations/         # Migration files
├── public/                 # Web root
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript
│   └── uploads/            # Uploaded files
├── resources/
│   └── views/              # View templates
│       ├── layouts/        # Layout templates
│       ├── admin/          # Admin templates
│       └── ...             # Feature templates
└── em-bazar-shop-db.sql    # Initial database dump
```

## License

MIT License
