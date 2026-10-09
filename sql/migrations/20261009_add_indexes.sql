-- =============================================================
-- Add missing performance indexes (idempotent, safe to re-run)
-- CU Official Giftshop - CodeIgniter 3
--
-- Usage:
--   mysql -h 127.0.0.1 -u root db_CUGiftshop < sql/migrations/20261009_add_indexes.sql
--
-- Every index is guarded by an information_schema existence check so the
-- script can be run on any database without erroring on already-added keys.
-- =============================================================

USE db_CUGiftshop;

DROP PROCEDURE IF EXISTS cu_add_index;
DELIMITER $$
CREATE PROCEDURE cu_add_index(IN tbl VARCHAR(64), IN idx VARCHAR(64), IN ddl VARCHAR(255))
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = tbl
          AND INDEX_NAME = idx
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE `', tbl, '` ADD ', ddl);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

-- products: catalog category filter + SKU uniqueness
CALL cu_add_index('products', 'idx_category', 'INDEX `idx_category` (`category_id`)');
CALL cu_add_index('products', 'sku', 'UNIQUE INDEX `sku` (`sku`)');

-- reservations: status tabs/counts, per-user history (ordered), global recency
CALL cu_add_index('reservations', 'idx_reservations_status', 'INDEX `idx_reservations_status` (`status`)');
CALL cu_add_index('reservations', 'idx_reservations_user_created', 'INDEX `idx_reservations_user_created` (`user_id`, `created_at`)');
CALL cu_add_index('reservations', 'idx_reservations_created', 'INDEX `idx_reservations_created` (`created_at`)');

-- reservation_items: product joins for best-sellers and reservation details
CALL cu_add_index('reservation_items', 'idx_item_product', 'INDEX `idx_item_product` (`product_id`)');

DROP PROCEDURE IF EXISTS cu_add_index;
