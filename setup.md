# 🚀 راهنمای راه‌اندازی پروژه بازار (Bazar Shop)

## فهرست مطالب
- [روش‌های راه‌اندازی](#روش‌های-راه‌اندازی)
- [روش ۱: استفاده از SQLite (سریع‌ترین)](#روش-۱-استفاده-از-sqlite-سریع‌ترین)
- [روش ۲: استفاده از MySQL](#روش-۲-استفاده-از-mysql)
- [روش ۳: استفاده از Docker](#روش-۳-استفاده-از-docker)
- [اطلاعات ورود](#اطلاعات-ورود)
- [ساختار پروژه](#ساختار-پروژه)

---

## روش‌های راه‌اندازی

### ✅ پیش‌نیازها

#### برای SQLite (توصیه می‌شود):
- PHP 8.0 یا بالاتر
- فعال بودن افزونه `pdo_sqlite`

#### برای MySQL:
- PHP 8.0 یا بالاتر
- MySQL 5.7+ یا MariaDB 10.3+
- فعال بودن افزونه `pdo_mysql`

#### برای Docker:
- Docker Desktop
- Docker Compose

---

## روش ۱: استفاده از SQLite (سریع‌ترین)

این روش نیازی به نصب MySQL ندارد و در کمتر از ۱ دقیقه آماده می‌شود!

### مراحل:

```bash
# 1. کپی کردن پروژه
cd /workspace

# 2. ایجاد فایل .env
cp .env.example .env

# 3. تنظیم SQLite در فایل .env
# فایل .env را باز کرده و مقادیر زیر را تنظیم کنید:
# DB_DRIVER=sqlite
# DB_DATABASE=sqlite/bazar.db

# 4. اجرای اسکریپت نصب
chmod +x setup.sh
./setup.sh

# 5. انتخاب گزینه 2 (SQLite)
```

### یا به صورت دستی:

```bash
# ایجاد فایل .env
echo "APP_NAME=\"بازار\"" > .env
echo "APP_URL=\"http://localhost/bazarEshopWithPhp/public/\"" >> .env
echo "APP_DEBUG=true" >> .env
echo "DB_DRIVER=sqlite" >> .env
echo "DB_DATABASE=sqlite/bazar.db" >> .env

# اجرای migration
sqlite3 database/sqlite/bazar.db < database/sqlite/schema.sql
```

✅ **تمام!** حالا به آدرس `http://localhost/bazarEshopWithPhp/public/` بروید.

---

## روش ۲: استفاده از MySQL

### مراحل:

```bash
# 1. ساخت دیتابیس
mysql -u root -p
CREATE DATABASE em-bazar-shop-db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

# 2. کپی کردن فایل .env
cp .env.example .env

# 3. ویرایش فایل .env و تنظیم اطلاعات MySQL
nano .env
# تغییرات لازم:
# DB_DRIVER=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=em-bazar-shop-db
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 4. اجرای migration
mysql -u root -p em-bazar-shop-db < database/migrations/001_add_modernization_tables.sql

# 5. یا استفاده از اسکریپت نصب
./setup.sh
# گزینه 1 (MySQL) را انتخاب کنید
```

---

## روش ۳: استفاده از Docker

### 🐳 راه‌اندازی با Docker Compose

این ساده‌ترین روش برای راه‌اندازی کامل پروژه است!

#### مراحل:

```bash
# 1. رفتن به پوشه پروژه
cd /workspace

# 2. اجرای Docker Compose
docker-compose up -d

# 3. مشاهده لاگ‌ها
docker-compose logs -f
```

**تمام!** پروژه شما روی `http://localhost:8080` در دسترس است.

### 🔧 تنظیمات Docker Compose

فایل `docker-compose.yml` شامل سرویس‌های زیر است:

```yaml
version: '3.8'

services:
  # وب سرور Apache با PHP 8.0
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: bazar_app
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./database/sqlite:/var/www/html/database/sqlite
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html/public
    depends_on:
      - db
  
  # دیتابیس MySQL
  db:
    image: mysql:8.0
    container_name: bazar_db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: em-bazar-shop-db
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_USER: bazar_user
      MYSQL_PASSWORD: bazar_password
    volumes:
      - mysql_data:/var/lib/mysql
      - ./database/migrations:/docker-entrypoint-initdb.d
    ports:
      - "3306:3306"
  
  # phpMyAdmin برای مدیریت دیتابیس
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: bazar_phpmyadmin
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: rootpassword
    ports:
      - "8081:80"
    depends_on:
      - db

volumes:
  mysql_data:
```

### 📦 ساخت Docker Image به صورت دستی

```bash
# ساخت image
docker build -t bazar-shop .

# اجرای container
docker run -d \
  --name bazar-shop \
  -p 8080:80 \
  -v $(pwd):/var/www/html \
  bazar-shop
```

### 🛑 توقف Docker

```bash
# توقف همه سرویس‌ها
docker-compose down

# توقف و حذف volume‌ها (دیتابیس پاک می‌شود!)
docker-compose down -v
```

### 📝 دستورات مفید Docker

```bash
# مشاهده وضعیت سرویس‌ها
docker-compose ps

# مشاهده لاگ‌ها
docker-compose logs app
docker-compose logs db

# ری‌استارت کردن سرویس‌ها
docker-compose restart

# اجرای دستور در container
docker-compose exec app php -v
docker-compose exec app ls -la

# ورود به shell داخل container
docker-compose exec app bash

# بیلد مجدد image
docker-compose build --no-cache

# آپدیت کردن سرویس‌ها
docker-compose up -d --force-recreate
```

---

## اطلاعات ورود

### پنل ادمین:
- **آدرس:** `http://localhost:8080/admin/` (Docker) یا `http://localhost/bazarEshopWithPhp/public/admin/` (Local)
- **ایمیل:** `admin@bazar.local`
- **رمز عبور:** `admin123`

---

## ساختار پروژه

```
/workspace
├── app/                    # فایل‌های اصلی برنامه
├── admin/                  # پنل مدیریت
├── blog/                   # بخش بلاگ
├── user/                   # صفحات کاربری
├── css/                    # فایل‌های CSS
│   ├── style.css          # استایل‌های سفارشی
│   └── bootstrap.rtl.min.css
├── js/                     # فایل‌های JavaScript
├── img/                    # تصاویر
├── php/                    # فایل‌های PHP
│   └── inc/               # فایل‌های شامل
│       ├── database.php   # اتصال به دیتابیس
│       ├── function.php   # توابع اصلی
│       └── action.php     # اکشن‌ها
├── database/              # فایل‌های دیتابیس
│   ├── sqlite/           # SQLite schema
│   │   └── schema.sql
│   └── migrations/       # MySQL migrations
├── public/                # نقطه ورود اصلی
├── resources/             # منابع اضافی
├── .env                   # فایل تنظیمات
├── .env.example          # نمونه فایل تنظیمات
├── setup.sh              # اسکریپت نصب
├── setup.md              # این فایل
├── README.md             # مستندات اصلی
├── Dockerfile            # Docker image
└── docker-compose.yml    # Docker Compose config
```

---

## عیب‌یابی

### ❌ خطای اتصال به دیتابیس

**برای SQLite:**
```bash
# بررسی مجوز پوشه
chmod -R 755 database/
chmod -R 777 database/sqlite/
```

**برای MySQL:**
- بررسی کنید سرویس MySQL در حال اجرا باشد
- اطلاعات اتصال در `.env` را بررسی کنید
- از وجود دیتابیس مطمئن شوید

### ❌ خطای Permission در Docker

```bash
# اصلاح مالکیت فایل‌ها
sudo chown -R www-data:www-data /workspace
```

### ❌ خطای PDO Extension

```bash
# نصب افزونه SQLite
apt-get install php-sqlite3
# یا برای MySQL
apt-get install php-mysql

# ری‌استارت Apache
service apache2 restart
```

---

## نکات امنیتی

1. **در Production:**
   - `APP_DEBUG=false` قرار دهید
   - رمز عبور ادمین را تغییر دهید
   - از HTTPS استفاده کنید

2. **فایل .env:**
   - هرگز این فایل را commit نکنید
   - در `.gitignore` قرار دارد

3. **رمز عبورها:**
   - از رمزهای قوی استفاده کنید
   - به صورت خودکار با bcrypt هش می‌شوند

---

## پشتیبانی

برای گزارش مشکلات یا درخواست ویژگی‌های جدید، لطفاً از طریق GitHub Issues اقدام کنید.

**موفق باشید! 🎉**
