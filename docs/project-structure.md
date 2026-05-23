# TechGear Project Structure

This project uses a small PHP/XAMPP structure with a separated public web root.

```text
TechGear/
├── app/
│   ├── core/              # Application startup and database connection
│   ├── support/           # Shared helper functions and business helpers
│   └── views/partials/    # Shared header/footer partials
├── config/                # Application and database configuration
├── database/              # SQL schema and seed data
├── docs/                  # Project documentation
├── public/                # Browser-accessible pages and static assets
│   └── assets/
│       ├── css/           # Global, component, page, and admin styles
│       ├── images/        # Product, hero, and placeholder images
│       └── js/            # Browser JavaScript
└── storage/               # Runtime logs/cache placeholders
```

## Where Files Belong

- Put customer-facing PHP pages in `public/`.
- Put browser-only CSS, JavaScript, and images under `public/assets/`.
- Put shared PHP startup, database, session, cart, product, and order helpers under `app/`.
- Put reusable page fragments under `app/views/partials/`.
- Put database schema and seed scripts under `database/`.
- Put non-runtime documentation under `docs/`.
- Put generated logs and cache files under `storage/`.

The root `.htaccess` routes old URLs such as `index.php`, `products.php`, and `images/...` to the new `public/` structure.
