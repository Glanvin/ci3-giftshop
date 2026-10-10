-- Add the receipt (OR) number captured when a reservation is completed.
-- Usage: mysql -h 127.0.0.1 -u root db_CUGiftshop < sql/migrations/20261010_add_reservation_or_number.sql

USE db_CUGiftshop;

DROP PROCEDURE IF EXISTS cu_add_reservation_or_number;
DELIMITER $$
CREATE PROCEDURE cu_add_reservation_or_number()
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'reservations'
          AND COLUMN_NAME = 'or_number'
    ) THEN
        ALTER TABLE reservations ADD COLUMN or_number VARCHAR(50) NULL AFTER receipt_image;
    END IF;
END$$
DELIMITER ;

CALL cu_add_reservation_or_number();
DROP PROCEDURE IF EXISTS cu_add_reservation_or_number;
