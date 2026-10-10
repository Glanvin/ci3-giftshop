-- Create inventory movement logging and retain stock-in reference details.
-- Usage: mysql -h 127.0.0.1 -u root db_CUGiftshop < sql/migrations/20261010_add_inventory_logs.sql

USE db_CUGiftshop;

CREATE TABLE IF NOT EXISTS inventory_logs (
  id                INT(11) NOT NULL AUTO_INCREMENT,
  product_id        INT(11) NOT NULL,
  user_id           INT(11) NOT NULL,
  action            ENUM('add','remove','update','reservation','fulfillment') NOT NULL,
  quantity_change   INT NOT NULL,
  previous_quantity INT NOT NULL,
  new_quantity      INT NOT NULL,
  reference_no      VARCHAR(100) NULL,
  supplier          VARCHAR(150) NULL,
  notes             TEXT NULL,
  created_at        TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_inventory_logs_product_created (product_id, created_at),
  KEY idx_inventory_logs_user_created (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP PROCEDURE IF EXISTS cu_add_inventory_log_details;
DELIMITER $$
CREATE PROCEDURE cu_add_inventory_log_details()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'inventory_logs'
          AND COLUMN_NAME = 'reference_no'
    ) THEN
        ALTER TABLE inventory_logs ADD COLUMN reference_no VARCHAR(100) NULL AFTER new_quantity;
    END IF;

    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'inventory_logs'
          AND COLUMN_NAME = 'supplier'
    ) THEN
        ALTER TABLE inventory_logs ADD COLUMN supplier VARCHAR(150) NULL AFTER reference_no;
    END IF;
END$$
DELIMITER ;

CALL cu_add_inventory_log_details();
DROP PROCEDURE IF EXISTS cu_add_inventory_log_details;
