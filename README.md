# بازار - فروشگاه آنلاین

فروشگاه آنلاین ساخته شده با PHP خالص (Vanilla PHP) با پشتیبانی از زبان فارسی و راست‌چین (RTL).

> این پروژه یکی از اولین کارهای من با PHP بود. بعد از چند سال تصمیم گرفتم دوباره بهش سر بزنم و با ساختار مدرن بازنویسیش کنم — از MVC و prepared statements گرفته تا داشبورد مدیریت و UI بهتر.

---

# Bazar - Online Store

An online store built with vanilla PHP with full Persian/Farsi RTL support.

> This was one of my first PHP projects. After a few years I decided to revisit it and rewrite it with a modern architecture — from MVC and prepared statements to an admin dashboard and improved UI.

---

## امکانات / Features

### فروشگاه / Shop
- نمایش محصولات با دسته‌بندی و فیلتر — Product listing with category filtering
- جستجوی محصولات — Product search
- سبد خرید با مدیریت تعداد — Cart with quantity management
- فرآیند تکمیل خرید — Checkout flow
- پیگیری سفارشات — Order tracking

### پنل مدیریت / Admin Dashboard
- داشبورد با نمودارهای آماری (Chart.js) — Dashboard with statistical charts
- مدیریت محصولات (افزودن، ویرایش، حذف) — Product CRUD management
- مدیریت موجودی انبار — Inventory management
- مدیریت سفارشات با چرخه کامل وضعیت — Full order lifecycle management
- مدیریت کاربران و نقش‌ها — User and role management
- مدیریت بلاگ با ویرایشگر متن غنی (CKEditor) — Blog CMS with rich text editor
- دسته‌بندی محصولات و پست‌ها — Product and post categories

### امنیت / Security
- عبارات آماده (Prepared Statements) برای تمام کوئری‌های دیتابیس — Prepared statements for all database queries
- رمزگذاری رمز عبور با bcrypt — Password hashing with bcrypt
- محافظت CSRF در تمام فرم‌ها — CSRF protection on all forms
- اعتبارسنجی ورودی در سمت سرور — Server-side input validation
- جلوگیری از XSS — XSS prevention
- آپلود امن فایل‌ها — Secure file uploads

### UI/UX
- طراحی واکنش‌گرا (Mobile Responsive) — Fully responsive design
- ناوبری موبایل با نوار پایین — Mobile bottom navigation bar
- اعلان‌های Toast با بسته شدن خودکار — Auto-dismissing toast notifications
- دکمه بازگشت به بالا — Scroll-to-top button
- پشتیبانی RTL کامل — Full RTL support

---

## تکنولوژی‌ها / Tech Stack

- **Backend:** PHP 8.0+ (Vanilla PHP)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5 RTL, Bootstrap Icons
- **Charts:** Chart.js
- **Editor:** CKEditor 5
- **Font:** Vazir

---

## پیش‌نیازها / Prerequisites

- PHP 8.0 یا بالاتر / PHP 8.0 or higher
- MySQL 5.7+ یا MariaDB 10.3+ / MySQL 5.7+ or MariaDB 10.3+
- قابلیت URL Rewrite (Apache mod_rewrite یا Nginx) / URL Rewrite support (Apache mod_rewrite or Nginx)

---

## نصب / Installation

1. **کلون کردن پروژه / Clone the project**
```bash
git clone https://github.com/daniru/bazarEshopWithPhp.git
cd bazarEshopWithPhp
```

2. **تنظیم محیط / Setup environment**
```bash
cp .env.example .env
```
فایل `.env` را ویرایش کنید و اطلاعات دیتابیس را وارد کنید.
Edit `.env` and fill in your database credentials.

3. **ایجاد دیتابیس و جداول / Create database and tables**
```bash
mysql -u root -p -e "CREATE DATABASE `em-bazar-shop-db`"
mysql -u root -p em-bazar-shop-db < em-bazar-shop-db.sql
mysql -u root -p em-bazar-shop-db < database/migrations/001_add_modernization_tables.sql
```

4. **تنظیم Web Server / Configure Web Server**
   - Document Root را روی پوشه `public/` تنظیم کنید / Set Document Root to `public/`
   - مطمئن شوید mod_rewrite فعال است / Make sure mod_rewrite is enabled

5. **ورود به پنل مدیریت / Access Admin Panel**
   - آدرس: `http://your-domain/admin` / URL: `http://your-domain/admin`
   - ایمیل و رمز عبور کاربر ادمین موجود در دیتابیس را استفاده کنید / Use the admin credentials from the database

---

## ساختار پروژه / Project Structure

```
├── app/                    # کد اصلی اپلیکیشن / Core application code
│   ├── Config/             # پیکربندی و مسیریاب / Config and router
│   ├── Controllers/        # کنترلرها / Controllers
│   │   └── Admin/          # کنترلرهای پنل مدیریت / Admin controllers
│   ├── Database/           # اتصال دیتابیس / Database connection
│   ├── Helpers/            # ابزارهای کمکی / Helper utilities
│   ├── Middleware/          # میان‌افزارها / Middleware (auth, CSRF)
│   └── Models/             # مدل‌ها / Models
├── database/
│   └── migrations/         # فایل‌های مایگریشن / Migration files
├── public/                 # روت وب / Web root
│   ├── css/                # فایل‌های استایل / Stylesheets
│   ├── js/                 # فایل‌های جاوااسکریپت / JavaScript
│   └── uploads/            # فایل‌های آپلود شده / Uploaded files
├── resources/
│   └── views/              # قالب‌های نمایش / View templates
│       ├── layouts/        # قالب‌های اصلی / Layout templates
│       ├── admin/          # قالب‌های پنل مدیریت / Admin templates
│       └── ...             # قالب‌های بخش‌های مختلف / Feature templates
└── em-bazar-shop-db.sql    # فایل دیتابیس اولیه / Initial database dump
```

---

## مجوز / License

MIT License
