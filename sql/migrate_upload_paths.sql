-- Run once after moving legacy files into uploads/products and uploads/receipt.
-- This keeps database URLs in step with the new on-disk upload folders.

UPDATE products
SET image_url = CONCAT('uploads/products/', SUBSTRING(image_url, LOCATE('/', image_url) + 1))
WHERE image_url IS NOT NULL
  AND LOWER(image_url) REGEXP '^(product-images|product_images|uploads/products)/';

UPDATE reservations
SET receipt_image = CONCAT('uploads/receipt/', SUBSTRING(receipt_image, LOCATE('/', receipt_image) + 1))
WHERE receipt_image IS NOT NULL
  AND LOWER(receipt_image) REGEXP '^(receipts|receipt|uploads/receipts|uploads/receipt)/';
