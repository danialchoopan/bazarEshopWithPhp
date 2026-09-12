# راهنمای راه‌اندازی پروژه Bazar Shop

## 🚀 روش‌های نصب

### روش ۱: SQLite (سریع‌ترین - پیشنهادی)

```bash
# کپی فایل تنظیمات
cp .env.example .env

# ویرایش فایل .env و تنظیم DB_DRIVER=sqlite
nano .env

# اجرای اسکریپت نصب
./setup.sh
# گزینه ۲ را انتخاب کنید (SQLite)
```

**مزایا:**
- ✅ بدون نیاز به MySQL
- ✅ راه‌اندازی در ۳۰ ثانیه
- ✅ مناسب برای توسعه و تست

---

### روش ۲: MySQL

```bash
# کپی فایل تنظیمات
cp .env.example .env

# ویرایش و تنظیم اطلاعات MySQL
nano .env

# ساخت دیتابیس
mysql -u root -p
CREATE DATABASE bazar_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# اجرای اسکریپت نصب
./setup.sh
# گزینه ۱ را انتخاب کنید (MySQL)
```

---

### روش ۳: Docker (کامل‌ترین)

```bash
# اجرای Docker Compose
docker-compose up -d

# مشاهده لاگ‌ها
docker-compose logs -f

# دسترسی به phpMyAdmin
# http://localhost:8081
```

**سرویس‌ها:**
- وب‌سرور: http://localhost:8080
- MySQL: localhost:3306
- phpMyAdmin: http://localhost:8081

---

## 📦 اجرای Migrationها

```bash
# اجرای همه migrationها
php database/migrate.php up

# مشاهده وضعیت
php database/migrate.php status

# بازگشت آخرین migration
php database/migrate.php down
```

---

## 🔐 ورود به پنل مدیریت

- آدرس: http://localhost/bazarEshopWithPhp/admin/dashboard.php
- ایمیل: `admin@bazar.local`
- رمز: `admin123`

---

## 🎯 ویژگی‌های جدید

### امنیتی
- ✅ محافظت CSRF
- ✅ کنترل دسترسی مبتنی بر نقش (RBAC)
- ✅ اعتبارسنجی ورودی‌ها
- ✅ مدیریت خطا و لاگ‌گیری

### فروشگاهی
- ✅ سیستم کوپن تخفیف
- ✅ مدیریت موجودی انبار
- ✅ پیگیری سفارشات
- ✅ محاسبه هزینه ارسال
- ✅ درگاه پرداخت زرین‌پال

### UI/UX
- ✅ حالت شب/روز
- ✅ نوتیفیکیشن Toast
- ✅ انیمیشن‌های اسکرول
- ✅ جستجوی زنده
- ✅ نمایش سریع محصول

---

## 📁 ساختار پوشه‌ها

```
/workspace
├── core/                  # کلاس‌های اصلی
│   ├── Auth.php          # کنترل دسترسی
│   ├── Csrf.php          # محافظت CSRF
│   ├── Validator.php     # اعتبارسنجی
│   ├── ErrorHandler.php  # مدیریت خطا
│   ├── CouponManager.php # سیستم کوپن
│   └── ZarinPalPayment.php # پرداخت
├── database/
│   ├── migrate.php       # ابزار migration
│   ├── migrations/       # فایل‌های migration
│   └── sqlite/           # دیتابیس SQLite
├── public/
│   ├── js/main.js        # جاوااسکریپت اصلی
│   └── payment/          # صفحات پرداخت
├── admin/                # پنل مدیریت
├── logs/                 # فایل‌های لاگ
└── setup.md             # این فایل
```

---

## 🔧 تنظیمات محیطی (.env)

```env
# محیط اجرا
APP_ENV=development
APP_URL=http://localhost/bazarEshopWithPhp/public

# دیتابیس
DB_DRIVER=sqlite  # یا mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bazar_shop
DB_USERNAME=root
DB_PASSWORD=

# زرین‌پال
ZARINPAL_SANDBOX=true
ZARINPAL_MERCHANT_ID=879daed9-18e0-4a64-b01f-2ed3c0a739b5
```

---

## 🐛 رفع مشکلات

### خطای اتصال به دیتابیس
```bash
# بررسی فایل .env
cat .env

# بررسی مجوزهای SQLite
chmod 755 database/sqlite
chmod 644 database/sqlite/*.db
```

### خطای PHP
```bash
# بررسی نسخه PHP
php -v

# بررسی افزونه‌ها
php -m | grep -E 'pdo|curl|json'
```

### پاک‌سازی کش
```bash
# حذف فایل‌های موقت
rm -rf logs/*
rm database/sqlite/*.db  # فقط برای SQLite
```

---

## 📞 پشتیبانی

برای گزارش مشکلات یا درخواست ویژگی‌های جدید، لطفاً از طریق Issues گیت‌هاب اقدام کنید.
