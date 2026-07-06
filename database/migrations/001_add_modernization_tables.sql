-- Migration: Modernization tables and columns
-- Run this AFTER the original em-reza-shop-db.sql

-- =============================================
-- ALTER EXISTING TABLES
-- =============================================

-- Products: add stock, slug, is_active, fix price type
ALTER TABLE `products`
  ADD COLUMN `stock` INT NOT NULL DEFAULT 0 AFTER `category_product_id`,
  ADD COLUMN `slug` VARCHAR(255) NOT NULL DEFAULT '' AFTER `stock`,
  ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `slug`;

-- Fix price column from TEXT to DECIMAL
ALTER TABLE `products`
  MODIFY COLUMN `price` DECIMAL(12,0) NOT NULL DEFAULT 0;

-- Orders: add timestamps, total, tracking_code, fix status
ALTER TABLE `orders`
  ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `phone`,
  ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
  ADD COLUMN `total` DECIMAL(12,0) NOT NULL DEFAULT 0 AFTER `updated_at`,
  ADD COLUMN `tracking_code` VARCHAR(50) DEFAULT NULL AFTER `total`,
  ADD COLUMN `status_text` VARCHAR(50) NOT NULL DEFAULT 'pending' AFTER `tracking_code`;

-- Update existing status values (0 = pending, 1 = delivered)
UPDATE `orders` SET `status_text` = 'pending' WHERE `status` = 0;
UPDATE `orders` SET `status_text` = 'delivered' WHERE `status` = 1;

-- Cart: add quantity
ALTER TABLE `cart`
  ADD COLUMN `quantity` INT NOT NULL DEFAULT 1 AFTER `product_id`;

-- Users: add avatar, status, created_at
ALTER TABLE `users`
  ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL AFTER `password`,
  ADD COLUMN `status` ENUM('active','inactive','banned') NOT NULL DEFAULT 'active' AFTER `avatar`;

-- =============================================
-- NEW TABLES
-- =============================================

-- Order items (replace serialized products)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(12,0) NOT NULL DEFAULT 0,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Role-Permission mapping
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User-Role mapping
CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SEED DEFAULT DATA
-- =============================================

-- Default roles
INSERT INTO `roles` (`name`, `description`) VALUES
  ('super_admin', 'مدیر ارشد - دسترسی کامل'),
  ('manager', 'مدیر - مدیریت محصولات و سفارشات'),
  ('editor', 'ویرایشگر - مدیریت محتوا و پست‌ها'),
  ('support', 'پشتیبانی - مشاهده سفارشات');

-- Default permissions
INSERT INTO `permissions` (`name`, `description`) VALUES
  ('manage_products', 'مدیریت محصولات'),
  ('manage_orders', 'مدیریت سفارشات'),
  ('manage_users', 'مدیریت کاربران'),
  ('manage_posts', 'مدیریت پست‌ها'),
  ('view_dashboard', 'مشاهده داشبورد'),
  ('manage_settings', 'مدیریت تنظیمات'),
  ('manage_roles', 'مدیریت نقش‌ها');

-- Assign all permissions to super_admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r, `permissions` p WHERE r.name = 'super_admin';

-- Assign relevant permissions to manager
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r, `permissions` p
WHERE r.name = 'manager' AND p.name IN ('manage_products', 'manage_orders', 'view_dashboard');

-- Assign relevant permissions to editor
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r, `permissions` p
WHERE r.name = 'editor' AND p.name IN ('manage_posts', 'view_dashboard');

-- Assign relevant permissions to support
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r, `permissions` p
WHERE r.name = 'support' AND p.name IN ('manage_orders', 'view_dashboard');

-- Assign super_admin role to existing admin user (role=1)
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`)
SELECT u.id, r.id FROM `users` u, `roles` r WHERE u.role = 1 AND r.name = 'super_admin';
