CREATE DATABASE IF NOT EXISTS techgear CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE techgear;

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS newsletter_subscribers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    phone VARCHAR(40) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    category VARCHAR(60) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    tag VARCHAR(40) DEFAULT NULL,
    brand VARCHAR(120) NOT NULL DEFAULT 'TechGear Originals',
    warranty VARCHAR(120) NOT NULL DEFAULT '2 Years Limited',
    stock INT UNSIGNED NOT NULL DEFAULT 25,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_products_category (category),
    INDEX idx_products_featured (is_featured),
    INDEX idx_products_status (status)
) ENGINE=InnoDB;

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    order_number VARCHAR(40) NOT NULL UNIQUE,
    subtotal DECIMAL(12, 2) NOT NULL,
    tax DECIMAL(12, 2) NOT NULL,
    shipping DECIMAL(12, 2) NOT NULL DEFAULT 0,
    total DECIMAL(12, 2) NOT NULL,
    status ENUM('processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'processing',
    shipping_name VARCHAR(120) NOT NULL,
    shipping_email VARCHAR(160) NOT NULL,
    shipping_phone VARCHAR(40) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED DEFAULT NULL,
    product_name VARCHAR(160) NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    phone VARCHAR(40) DEFAULT NULL,
    subject VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'archived') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(160) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (name, email, password_hash, role, phone, address) VALUES
('Admin Master', 'admin@techgear.local', '$2y$10$tiPhFbjmtmiAAzLSVKW0J.1GNSTjEcT1zEtUBqzmSZytPiEQeyopS', 'admin', '+94 11 234 5678', '45/2 Galle Road, Colombo 03'),
('Gamer User', 'user@techgear.local', '$2y$10$zw6VKUgizNjZCIiXBmx.9.DhFx8GtlhroOnDXKoJGCnBbqFOvTm7C', 'customer', '+94 77 123 4567', 'Colombo, Sri Lanka');

INSERT INTO products (name, category, price, image, description, tag, brand, warranty, stock, is_featured) VALUES
('Viper Ultimate RGB', 'mice', 45000.00, 'assets/images/viper_ultimate_rgb.png', 'Ultra-fast wireless gaming mouse with optical switches, low-latency tracking, and customizable RGB lighting.', 'NEW', 'Razer', '2 Years Limited', 42, 1),
('Mech Pro TKL Keyboard', 'keyboards', 55000.00, 'assets/images/mech_pro_tkl.png', 'Tenkeyless mechanical keyboard with tactile switches, aluminum top plate, hot-swap PCB, and per-key RGB.', NULL, 'Keychron', '2 Years Limited', 31, 1),
('Aura Sync Surround Headset', 'headsets', 65000.00, 'assets/images/aura_sync_headset.png', '7.1 surround sound gaming headset with noise-canceling microphone, memory foam earcups, and clean voice pickup.', 'HOT', 'ASUS', '2 Years Limited', 24, 1),
('RTX 4080 Super GPU', 'components', 425000.00, 'assets/images/rtx_4080_super.png', 'High-end graphics card built for smooth 4K gaming, advanced ray tracing, and accelerated creative workloads.', NULL, 'NVIDIA', '3 Years Limited', 9, 1),
('Precision Glide Mousepad', 'accessories', 12000.00, 'assets/images/precision_mousepad.png', 'Extra-large textured cloth mousepad optimized for consistent tracking across low and high sensitivity settings.', NULL, 'TechGear', '1 Year Limited', 88, 0),
('Silent Switch Custom Keyboard', 'keyboards', 95000.00, 'assets/images/silent_custom_keyboard.png', 'Premium custom keyboard with lubed silent switches, gasket mount feel, PBT keycaps, and a compact layout.', 'SALE', 'TechGear Customs', '2 Years Limited', 16, 0),
('ErgoLift Gaming Desk', 'accessories', 165000.00, 'assets/images/ergolift_desk.png', 'Motorized sit-stand gaming desk with carbon texture desktop, memory height presets, and cable management.', NULL, 'TechGear Studio', '3 Years Limited', 7, 0),
('Core i9 14900K Processor', 'components', 235000.00, 'assets/images/core_i9_14900k.png', 'Unlocked 24-core desktop processor tuned for high-refresh gaming, streaming, and demanding multitasking.', 'HOT', 'Intel', '3 Years Limited', 14, 0),
('Pro Streamer RGB Mic', 'accessories', 55000.00, 'assets/images/gaming_mic.png', 'Studio-quality USB microphone with customizable RGB, built-in shock mount, gain control, and clear broadcast tone.', 'NEW', 'Elgato', '2 Years Limited', 29, 0);

INSERT INTO orders
(user_id, order_number, subtotal, tax, shipping, total, status, shipping_name, shipping_email, shipping_phone, shipping_address, created_at)
VALUES
(2, 'TG-260510-9421', 45000.00, 3600.00, 0.00, 48600.00, 'delivered', 'Gamer User', 'user@techgear.local', '+94 77 123 4567', 'Colombo, Sri Lanka', '2026-05-10 10:30:00'),
(2, 'TG-260518-9550', 425000.00, 34000.00, 0.00, 459000.00, 'shipped', 'Gamer User', 'user@techgear.local', '+94 77 123 4567', 'Colombo, Sri Lanka', '2026-05-18 15:45:00');

INSERT INTO order_items (order_id, product_id, product_name, price, quantity, image) VALUES
(1, 1, 'Viper Ultimate RGB', 45000.00, 1, 'assets/images/viper_ultimate_rgb.png'),
(2, 4, 'RTX 4080 Super GPU', 425000.00, 1, 'assets/images/rtx_4080_super.png');
