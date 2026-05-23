# TechGear
TechGear is a PHP and MySQL e-commerce web application built for XAMPP. It includes a customer storefront, product catalog, shopping cart, checkout flow, user accounts, contact form, newsletter subscription, and an admin dashboard.

## Features
- Customer home page, product listing, product detail, cart, checkout, contact, privacy, login, register, and profile pages
- Product search, category filters, and price filters
- Session-based shopping cart with quantity updates
- Checkout with order creation, order items, tax calculation, and stock reduction
- Customer registration, login, profile updates, and order history
- Contact messages saved to the database
- Newsletter subscriber storage
- Admin login separated from customer login
- Admin dashboard for products, users, orders, and support messages
- MySQL database installer with sample products, users, and orders

## Tech Stack
- PHP
- MySQL
- HTML
- CSS
- JavaScript
- XAMPP

## Requirements
- XAMPP installed
- Apache running
- MySQL running
- A browser such as Chrome, Edge, Firefox, or Safari

## Project Structure

TechGear/
├── app/
│   ├── core/              # Startup and database connection files
│   ├── support/           # Shared helper functions
│   └── views/partials/    # Header and footer partials
├── config/                # App and database configuration
├── database/              # SQL schema and seed data
├── docs/                  # Project documentation
├── public/                # Browser-accessible pages and assets
│   └── assets/
│       ├── css/           # Stylesheets
│       ├── images/        # Product and slider images
│       └── js/            # JavaScript
└── storage/               # Runtime cache and log folders
