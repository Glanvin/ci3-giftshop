# CU Official Giftshop — CodeIgniter 3 Conversion

A CodeIgniter 3 (CI3) MVC re-implementation of the procedural PHP giftshop
app found in the sibling `giftshop/` folder. Same look, same database, no
raw SQL in views.

## Requirements

- PHP 7.2+
- MySQL (tested on XAMPP: `127.0.0.1`, user `root`, empty password)
- Apache with `mod_rewrite` (the bundled root `.htaccess` provides the
  pretty URLs) — or run without rewriting via `index.php/shop`, etc.

## Setup

1. Create the database and tables, then seed demo data:

   ```sql
   -- run with your MySQL client / phpMyAdmin
   SOURCE sql/giftshop.sql;
   ```

   This creates `db_CUGiftshop` (categories, products, users, reservations,
   reservation_items) and loads demo users + sample products. Product
   images are stored in `product-images/` (paths in the DB are relative, so
   they already work — no CodeIgniter `/index.php` prefix needed).

2. Confirm DB credentials in `application/config/database.php`
   (defaults: host `127.0.0.1`, user `root`, password `''`, database
   `db_CUGiftshop`, driver `mysqli`).

3. If the site is NOT at `http://localhost/ci3-giftshop`, update
   `RewriteBase` in the root `.htaccess` (and `base_url` in
   `application/config/config.php`).

4. Serve the app, e.g. via Apache VirtualHost pointing at this folder.

## Demo Accounts

| Role    | Email              | Password     |
|---------|--------------------|--------------|
| Student | student@cu.edu.ph  | password123  |
| Staff   | staff@cu.edu.ph    | password123  |
| Admin   | admin@g.cu.edu.ph  | admin123     |

Passwords are stored in plain text for behavior-compatibility with the
original app's database (do not do this in production).

## Routes

| URL                          | Controller / Method                 |
|------------------------------|-------------------------------------|
| `/`                          | `HomeController::index`             |
| `/auth/login` `/auth/signup` | `AuthController`                    |
| `/shop`                      | `ShopController::index` (public)    |
| `/shop/product/<id>`         | `ShopController::product` (public)  |
| `/shop/browse`               | `ShopController::browse` (logged-in)|
| `/user`                      | `UserController::dashboard`         |
| `/user/product/<id>`         | `UserController::product`           |
| `/user/reservations`         | `UserController::my_reservations`   |
| `/user/reservation/<id>`     | `UserController::view_reservation`  |
| `/user/upload_receipt`       | `UserController::upload_receipt`    |
| `/cart` `cart/add` `cart/*`  | `CartController` (CI3 Cart library) |
| `/staff` `staff/*`           | `StaffController` (staff/admin only)|

## Migrating from the old app

The upload script imports the *existing* `giftshop` database as-is:

- `users` — plaintext passwords are compared directly.
- `products.image_url` — paths like `Product-Images/...` are normalized to
  `product-images/...` at render time (`ShopController::img_url`), and the
  CI3 `Upload` library writes new images into `product-images/`.
- `reservations` — a `receipt_image` column (absent in the original schema)
  is added by `sql/giftshop.sql`; `sql/giftshop.sql` targets a fresh
  database, so if a legacy `reservations` table already exists, run
  `ALTER TABLE reservations ADD COLUMN receipt_image VARCHAR(255) NULL`
  yourself.

## Notes

- `assets/` is served outside the CI3 `application` folder, mirroring the
  original project structure (CSS/JS copied verbatim).
- The old localStorage cart badge logic in `assets/js/main.js` was replaced
  by a server-rendered badge (`#cart-count`).
- Dead/left-over pages from the original (`user/userproductdetail.php`,
  `backend/tables/*`, `backend/db-create.php`, 0-byte `Images` file,
  `.gitattributes`, `forgot.php`) were not ported.