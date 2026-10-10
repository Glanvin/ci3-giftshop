# AGENTS.md

Guidance for AI coding agents working in this repository. Keep it short; see
`README.md` for full setup details.

## Project Overview

A **CodeIgniter 3** MVC e-commerce app: the CU Official Giftshop. It is a
conversion of a procedural PHP app (sibling `giftshop/` folder) sharing the
same database and look. No raw SQL in views.

Core domains:

- **Catalog** — products & categories, public browsing, search/filter/sort.
- **Auth** — login/signup/logout, roles `student` / `staff` / `admin`.
- **Cart** — server-side cart via the CI3 Cart library.
- **Reservations** — users reserve items; receipt upload; status workflow
  `pending → confirmed → ready → completed` (or `cancelled`).
- **Staff/Admin** — inventory & category CRUD, stock-in, reservation
  management, reports (revenue, trends, stock).

## Tech Stack

- CodeIgniter **3.1.13** (`system/core/CodeIgniter.php`).
- PHP **7.2+** (README); `composer.json` allows `>=5.3.7`.
- MySQL/MariaDB via the **mysqli** driver; database `db_CUGiftshop`.
- Bootstrap + static CSS/JS in `assets/`. **No npm / build step.**
- Composer autoload is **disabled**; no vendor deps are checked in.

## Setup / Running

- XAMPP-oriented. App is expected at `http://localhost/ci3-giftshop/`; the
  root `.htaccess` has `RewriteBase /ci3-giftshop/`.
- Import schema + demo data: `SOURCE sql/giftshop.sql;` (creates
  `categories`, `products`, `users`, `reservations`, `reservation_items`).
- DB config in `application/config/database.php` (defaults: host
  `127.0.0.1`, user `root`, empty password).
- If the path/URL differs, update `RewriteBase` (`.htaccess`) and `base_url`
  (`application/config/config.php`).
- **No `.env` support** — all config lives in `application/config/*.php`.

## Directory Map

```
application/
  config/        all CI configs (routes, autoload, form_rules, database...)
  controllers/   AuthController, CartController, HomeController,
                 ShopController, StaffController, UserController
  models/        User_model, Category_model, Product_model, Reservation_model
  views/         auth/ shop/ staff/ user/ templates/ errors/
  helpers/       notification_helper, pricing_helper, upload_path_helper
assets/          static CSS/JS
sql/             schema, migrations, one-off scripts
tests/           pricing_test.php (standalone)
uploads/         products/ and receipt/ (user + seeded uploads)
system/          CodeIgniter 3 core (do not edit)
```

## Conventions

- **Controllers**: PascalCase with a `Controller` suffix (e.g.
  `ShopController`). Public routes are declared in
  `application/config/routes.php`; default is `HomeController`.
- **Models**: PascalCase with a `_model` suffix (e.g. `User_model`).
  All DB access is Query Builder in models; controllers stay thin.
- **Views**: loaded individually with `$this->load->view()` — there is **no
  layout engine**. Each page follows a header/footer template pattern:
  `templates/public_header` | `auth_header` | `user_header` | `staff_header`,
  then the page view, then `templates/footer`.
- **DB naming**: snake_case tables/columns.
- **Form validation**: rules centralized in
  `application/config/form_rules.php`; custom callbacks live in
  `StaffController`.
- **Notifications**: use the flash + PRG pattern via
  `render_notifications()` (`notification_helper.php`).
- **Pricing**: apply markup via `giftshop_price_with_markup()`
  (`pricing_helper.php`); returns `NULL` on invalid input.
- Helpers `notification_helper`, `pricing_helper`, `upload_path_helper` are
  autoloaded.

## Auth & Roles

- Session-based (`session` library autoloaded). Session stores `user_id`,
  `email`, `full_name`, `role`.
- Guards are in controller constructors: `UserController` redirects guests to
  login; `StaffController` requires `User_model::is_staff()` (true for `staff`
  and `admin`).

## Testing / Tooling

- Only test: `php tests/pricing_test.php` (standalone, no framework).
- PHPUnit is a dev dependency but there is **no working config** —
  `composer.json`'s `test:coverage` references a missing
  `tests/travis/sqlite.phpunit.xml`.
- No linter / code-style tooling is configured.

## Gotchas / Cautions

- **Plain-text passwords** are intentional for legacy DB compatibility. This
  is non-production; do not "fix" it without being asked.
- **CSRF protection is disabled** and global XSS filtering is off; the app
  escapes output explicitly with `html_escape()`. Tread carefully.
- `encryption_key` is empty and sessions use the files driver.
- **Uploads**: product images → `uploads/products/`, receipts →
  `uploads/receipt/` (max 5MB; jpg/png/gif/webp). Paths are normalized by the
  `upload_path_helper` functions, not stored as absolute paths.
- Submitting a reservation **decrements stock** (transactional) and sets a
  product to `out_of_stock` at zero. `reservation_items.price_at_time` is
  deliberately denormalized to preserve historical price.
- Reservation codes use the format `YYYY-LL-XXXXXX`.
- Reports/inventory use MySQL-specific boolean-sum / `GREATEST` / `IF` SQL.
- Never commit secrets, credentials, or `.env`-style config.

## Key Entry Points (Routes)

| URL                          | Controller / Method                  |
|------------------------------|--------------------------------------|
| `/`                          | `HomeController::index`              |
| `/auth/login` `/auth/signup` | `AuthController`                     |
| `/shop` `/shop/product/<id>` | `ShopController` (public)            |
| `/shop/browse`               | `ShopController::browse` (logged-in) |
| `/user` `/user/reservation/*`| `UserController`                     |
| `/cart` `/cart/*`            | `CartController` (CI3 Cart library)  |
| `/staff` `/staff/*`          | `StaffController` (staff/admin only) |
