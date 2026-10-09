-- =============================================================
-- CU Official Giftshop - MySQL schema (CodeIgniter 3 version)
-- Test: root @ 127.0.0.1, empty password, db_CUGiftshop
-- =============================================================

CREATE DATABASE IF NOT EXISTS db_CUGiftshop
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE db_CUGiftshop;

-- -------------------------------------------------------------
-- Categories
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name          VARCHAR(100) NOT NULL,
  display_order INT          NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Products
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name                VARCHAR(255)  NOT NULL,
  category_id         INT UNSIGNED  NOT NULL,
  description         TEXT          NULL,
  price               DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  stock_quantity      INT           NOT NULL DEFAULT 0,
  sku                 VARCHAR(50)   NULL,
  size                VARCHAR(20)   NULL,
  color               VARCHAR(50)   NULL,
  status              VARCHAR(20)   NOT NULL DEFAULT 'active',
  low_stock_threshold INT           NOT NULL DEFAULT 5,
  image_url           VARCHAR(255)  NULL,
  created_at          DATETIME      NOT NULL,
  updated_at          DATETIME      NOT NULL,
  PRIMARY KEY (id),
  KEY idx_category (category_id),
  UNIQUE KEY sku (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Users
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name   VARCHAR(150) NOT NULL,
  email       VARCHAR(150) NOT NULL,
  password    VARCHAR(255) NOT NULL,
  role        ENUM('student','staff','admin') NOT NULL DEFAULT 'student',
  phone       VARCHAR(30)  NULL,
  student_id  VARCHAR(30)  NULL,
  department  VARCHAR(100) NULL,
  status      ENUM('active','inactive') NOT NULL DEFAULT 'active',
  last_login  DATETIME     NULL,
  created_at  DATETIME     NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Reservations
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservations (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id          INT UNSIGNED NOT NULL,
  reservation_code VARCHAR(30)  NOT NULL,
  total_amount     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status           VARCHAR(20)  NOT NULL DEFAULT 'pending',
  notes            TEXT          NULL,
  receipt_image    VARCHAR(255)  NULL,
  expiry_date      DATETIME      NULL,
  created_at       DATETIME      NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_reservation_code (reservation_code),
  KEY idx_reservations_status (status),
  KEY idx_reservations_user_created (user_id, created_at),
  KEY idx_reservations_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -------------------------------------------------------------
-- Reservation Items
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reservation_items (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  reservation_id INT UNSIGNED NOT NULL,
  product_id     INT UNSIGNED NOT NULL,
  quantity       INT          NOT NULL DEFAULT 1,
  price_at_time  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id),
  KEY idx_item_reservation (reservation_id),
  KEY idx_item_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- Seed data
-- =============================================================

INSERT INTO categories (id, name, display_order) VALUES
  (1, 'Textbook',    1),
  (2, 'Uniform',     2),
  (3, 'PE Uniform',  3),
  (4, 'Merchandise', 4);

-- Demo logins:
--   staff@cu.edu.ph    / password123
--   student@cu.edu.ph  / password123
--   admin@g.cu.edu.ph  / admin123
INSERT INTO users (full_name, email, password, role, student_id, department, status, created_at) VALUES
  ('Staff Account', 'staff@cu.edu.ph',   'password123', 'staff',  NULL,         'Giftshop',     'active', NOW()),
  ('Student Account','student@cu.edu.ph', 'password123', 'student','CU-2026-001', 'Information Technology', 'active', NOW()),
  ('Admin Account', 'admin@g.cu.edu.ph', 'admin123',    'admin',   NULL,         'Administration','active', NOW());

INSERT INTO products
  (name, category_id, description, price, stock_quantity, sku, size, color, status, low_stock_threshold, image_url, created_at, updated_at)
VALUES
  ('StudyMate Academic Backpack', 4, 'Original StudyMate backpack for school and college.', 1299.00, 25, 'CU-BAG-001', NULL, 'Black', 'active', 5, 'uploads/products/1773833436_9-5-studymate-academic-backpack-for-school-and-college-with-original-imah3yhgaf5ckrz8.webp', NOW(), NOW()),
  ('University Long-Sleeve Tee', 4, 'Comfortable official university long-sleeve shirt.', 750.00, 40, 'CU-TEE-LS', 'XL', 'White', 'active', 5, 'uploads/products/1773881773_s-l1200.jpg', NOW(), NOW()),
  ('University Hoodie', 4, 'Official university hoodie, unisex fit.', 1200.00, 15, 'CU-HOOD-1', 'L', 'Maroon', 'active', 5, 'uploads/products/1774253752_university_hoodie.jpg', NOW(), NOW()),
  ('Logo Notebook Set', 4, 'College-ruled notebook with capitol university logo.', 350.00, 60, 'CU-NOTE-1', NULL, 'White', 'active', 5, 'uploads/products/1774254945_$_57.jpg', NOW(), NOW()),
  ('Alumni Mug', 4, 'Official collector alumni mug.', 280.00, 8, 'CU-MUG-01', NULL, 'Maroon', 'active', 5, 'uploads/products/1774267629_faye.jpg', NOW(), NOW()),
  ('PE Uniform Shirt', 3, 'Standard PE uniform shirt.', 450.00, 30, 'CU-PE-SHT', 'M', 'Red', 'active', 5, 'uploads/products/1774268779_Untitled design (5).png', NOW(), NOW()),
  ('School ID Lanyard', 4, 'Official lanyard with logo (pair of two).', 50.00, 0, 'CU-LAN-2', NULL, 'Maroon', 'out_of_stock', 5, 'uploads/products/1774338723_Untitled design (6).png', NOW(), NOW());