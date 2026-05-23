# TechGear PHP E-Commerce Project

TechGear is a PHP/MySQL project for XAMPP. The application now uses a cleaner PHP layout: browser-facing pages and assets live in `public/`, shared PHP code lives in `app/`, database settings live in `config/`, and the SQL installer schema lives in `database/`.

## Setup on XAMPP

1. Copy or keep this folder inside XAMPP `htdocs`, for example:
   `/Applications/XAMPP/xamppfiles/htdocs/TechGear`
2. Start Apache and MySQL from XAMPP.
3. Open:
   `http://localhost/TechGear/install.php`
4. Click **Run Installer**. This creates the `techgear` database, tables, seed products, demo users, and sample orders.
5. Open:
   `http://localhost/TechGear/index.php`

## Demo Logins

- Admin portal: `http://localhost/TechGear/admin-login.php`
- Admin: `admin@techgear.local` / `admin123`
- Customer: `user@techgear.local` / `user12345`

## Main Features

- Public `.php` pages contain the page UI and PHP backend logic together
- PHP/PDO MySQL backend with prepared statements
- Product catalog with search, category filters, and price filters
- Product detail pages
- Session cart with quantity updates and checkout
- Customer registration, login, profile updates, and order history
- Order creation with order items and stock reduction
- Contact form saved to the database
- Newsletter subscription storage
- Admin dashboard for products, users, orders, and support messages
- Dedicated admin portal separated from the storefront navigation
- Separate admin session from the customer storefront session

## Project Structure

- `public/` contains all browser-accessible PHP pages, legacy `.html` redirects, and static assets.
- `public/assets/css/` contains global, component, page, and admin stylesheets.
- `public/assets/js/app.js` contains small UI-only behavior such as mobile navigation and sliders.
- `public/assets/images/` contains product and hero images.
- `app/core/` contains application startup and database connection code.
- `app/support/` contains reusable helper functions for escaping, redirects, sessions, cart, products, orders, and assets.
- `app/views/partials/` contains shared page partials such as the header and footer.
- `config/` contains database and application path configuration.
- `database/schema.sql` contains the schema and seed data used by `public/install.php`.
- `docs/` contains project documentation and UI notes.
- Root `.htaccess` keeps existing URLs such as `/TechGear/index.php` working while routing execution to `public/`.

## Configuration

Database settings are in `config/config.php`.

Default XAMPP values are already set:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'techgear');
define('DB_USER', 'root');
define('DB_PASS', '');
```

If your MySQL root account has a password, update `DB_PASS`.
# TechGear
