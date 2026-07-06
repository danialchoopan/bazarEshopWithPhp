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
