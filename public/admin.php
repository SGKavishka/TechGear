<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$admin = require_admin_portal();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['admin_action'] ?? '';

    if ($action === 'save_product') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $image = trim($_POST['image'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $warranty = trim($_POST['warranty'] ?? '');
        $tag = trim($_POST['tag'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        $status = isset($_POST['status']) ? 1 : 0;

        if ($name === '' || !array_key_exists($category, categories()) || $price <= 0 || $image === '' || $description === '') {
            flash('error', 'Please complete all required product fields.');
            redirect('admin.php?section=products');
        }

        if ($productId > 0) {
            $stmt = db()->prepare(
                'UPDATE products
                SET name = ?, category = ?, price = ?, stock = ?, image = ?, brand = ?, warranty = ?, tag = ?, description = ?, is_featured = ?, status = ?
                WHERE id = ?'
            );
            $stmt->execute([
                $name,
                $category,
                $price,
                max(0, $stock),
                $image,
                $brand ?: 'TechGear Originals',
                $warranty ?: '2 Years Limited',
                $tag ?: null,
                $description,
                $isFeatured,
                $status,
                $productId,
            ]);
            flash('success', 'Product updated.');
        } else {
            $stmt = db()->prepare(
                'INSERT INTO products (name, category, price, stock, image, brand, warranty, tag, description, is_featured, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $name,
                $category,
                $price,
                max(0, $stock),
                $image,
                $brand ?: 'TechGear Originals',
                $warranty ?: '2 Years Limited',
                $tag ?: null,
                $description,
                $isFeatured,
                $status,
            ]);
            flash('success', 'Product created.');
        }

        redirect('admin.php?section=products');
    }

    if ($action === 'delete_product') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $stmt = db()->prepare('UPDATE products SET status = 0 WHERE id = ?');
        $stmt->execute([$productId]);
        flash('success', 'Product disabled.');
        redirect('admin.php?section=products');
    }

    if ($action === 'update_order_status') {
        $orderId = (int) ($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? 'processing';
        if (in_array($status, ['processing', 'shipped', 'delivered', 'cancelled'], true)) {
            $stmt = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $stmt->execute([$status, $orderId]);
            flash('success', 'Order status updated.');
        }
        redirect('admin.php?section=orders');
    }

    if ($action === 'mark_message_read') {
        $messageId = (int) ($_POST['message_id'] ?? 0);
        $stmt = db()->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
        $stmt->execute(['read', $messageId]);
        flash('success', 'Message marked as read.');
        redirect('admin.php?section=messages');
    }
}

$activeSection = $_GET['section'] ?? 'dashboard';
$activeSection = in_array($activeSection, ['dashboard', 'products', 'users', 'orders', 'messages'], true) ? $activeSection : 'dashboard';

$stats = [
    'revenue' => (float) db()->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status <> 'cancelled'")->fetchColumn(),
    'orders' => (int) db()->query("SELECT COUNT(*) FROM orders WHERE status IN ('processing', 'shipped')")->fetchColumn(),
    'users' => (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'low_stock' => (int) db()->query('SELECT COUNT(*) FROM products WHERE stock <= 10 AND status = 1')->fetchColumn(),
];

$products = db()->query('SELECT * FROM products ORDER BY status DESC, id ASC')->fetchAll();
$users = db()->query('SELECT id, name, email, role, phone, created_at FROM users ORDER BY id ASC')->fetchAll();
$orders = db()->query(
    'SELECT orders.*, users.name AS customer_name
    FROM orders
    JOIN users ON users.id = orders.user_id
    ORDER BY orders.created_at DESC'
)->fetchAll();
$messages = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 30')->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TechGear administration portal.">
    <title>Admin Portal | TechGear</title>
    <link rel="stylesheet" href="<?= h(asset_url('css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset_url('css/components.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset_url('css/admin.css')) ?>">
</head>

<body class="admin-portal-body">
    <header class="admin-portal-header">
        <a href="admin.php" class="admin-brand">
            <span class="admin-brand-mark">TG</span>
            <span>
                TechGear
                <strong>Operations Portal</strong>
            </span>
        </a>
        <div class="admin-session">
            <span><?= h($admin['name']) ?></span>
            <a href="index.php" class="btn btn-secondary compact-btn">View Store</a>
            <a href="admin-logout.php" class="btn btn-primary compact-btn">Sign Out</a>
        </div>
    </header>

    <?php if ($message = flash('success')): ?>
        <div class="flash-message flash-success"><?= h($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="flash-message flash-error"><?= h($message) ?></div>
    <?php endif; ?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <button class="admin-menu-item <?= $activeSection === 'dashboard' ? 'active' : '' ?>" data-target="dashboard-view" type="button">
            <span class="admin-menu-icon">DB</span>
            Overview
        </button>
        <button class="admin-menu-item <?= $activeSection === 'products' ? 'active' : '' ?>" data-target="products-view" type="button">
            <span class="admin-menu-icon">PR</span>
            Manage Products
        </button>
        <button class="admin-menu-item <?= $activeSection === 'users' ? 'active' : '' ?>" data-target="users-view" type="button">
            <span class="admin-menu-icon">US</span>
            User Accounts
        </button>
        <button class="admin-menu-item <?= $activeSection === 'orders' ? 'active' : '' ?>" data-target="orders-view" type="button">
            <span class="admin-menu-icon">OR</span>
            Orders
        </button>
        <button class="admin-menu-item <?= $activeSection === 'messages' ? 'active' : '' ?>" data-target="messages-view" type="button">
            <span class="admin-menu-icon">MS</span>
            Messages
        </button>
    </aside>

    <main class="admin-content">
        <section id="dashboard-view" class="admin-panel-section <?= $activeSection === 'dashboard' ? 'active' : '' ?>">
            <div class="admin-header">
                <div>
                    <span class="eyebrow">Admin</span>
                    <h1>Dashboard Overview</h1>
                </div>
                <a href="index.php" class="btn btn-secondary">View Store</a>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-blue">Rs</div>
                    <div>
                        <div class="stat-value"><?= format_price($stats['revenue']) ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-purple">OD</div>
                    <div>
                        <div class="stat-value"><?= $stats['orders'] ?></div>
                        <div class="stat-label">Active Orders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-green">US</div>
                    <div>
                        <div class="stat-value"><?= $stats['users'] ?></div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-orange">ST</div>
                    <div>
                        <div class="stat-value"><?= $stats['low_stock'] ?></div>
                        <div class="stat-label">Low Stock Alerts</div>
                    </div>
                </div>
            </div>

            <div class="admin-quick-grid">
                <a href="#products-view" class="admin-quick-card" data-jump="products-view">
                    <strong>Inventory</strong>
                    <span><?= count($products) ?> total products</span>
                </a>
                <a href="#orders-view" class="admin-quick-card" data-jump="orders-view">
                    <strong>Orders</strong>
                    <span><?= count($orders) ?> orders recorded</span>
                </a>
                <a href="#messages-view" class="admin-quick-card" data-jump="messages-view">
                    <strong>Support Inbox</strong>
                    <span><?= count($messages) ?> recent messages</span>
                </a>
            </div>
        </section>

        <section id="products-view" class="admin-panel-section <?= $activeSection === 'products' ? 'active' : '' ?>">
            <div class="table-header">
                <h2>Product Inventory</h2>
                <button class="btn btn-primary" type="button" onclick="openProductModal()">Add Product</button>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= (int) $product['id'] ?></td>
                            <td><img src="<?= h(product_image_url($product['image'])) ?>" alt="" class="admin-product-thumb"></td>
                            <td>
                                <strong><?= h($product['name']) ?></strong>
                                <span class="table-subtext"><?= h($product['brand']) ?></span>
                            </td>
                            <td><?= h(category_label($product['category'])) ?></td>
                            <td><?= format_price($product['price']) ?></td>
                            <td><?= (int) $product['stock'] ?></td>
                            <td>
                                <span class="status-pill <?= (int) $product['status'] === 1 ? 'status-live' : 'status-disabled' ?>">
                                    <?= (int) $product['status'] === 1 ? 'Live' : 'Disabled' ?>
                                </span>
                            </td>
                            <td class="action-cell">
                                <button
                                    class="action-btn action-edit"
                                    type="button"
                                    onclick="openProductModal(this)"
                                    data-id="<?= (int) $product['id'] ?>"
                                    data-name="<?= h($product['name']) ?>"
                                    data-category="<?= h($product['category']) ?>"
                                    data-price="<?= h((string) $product['price']) ?>"
                                    data-stock="<?= (int) $product['stock'] ?>"
                                    data-image="<?= h($product['image']) ?>"
                                    data-brand="<?= h($product['brand']) ?>"
                                    data-warranty="<?= h($product['warranty']) ?>"
                                    data-tag="<?= h((string) $product['tag']) ?>"
                                    data-description="<?= h($product['description']) ?>"
                                    data-featured="<?= (int) $product['is_featured'] ?>"
                                    data-status="<?= (int) $product['status'] ?>"
                                >Edit</button>
                                <form method="post" class="inline-form" onsubmit="return confirm('Disable this product?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="admin_action" value="delete_product">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <button class="action-btn action-delete" type="submit">Disable</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section id="users-view" class="admin-panel-section <?= $activeSection === 'users' ? 'active' : '' ?>">
            <div class="table-header">
                <h2>Registered Users</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $account): ?>
                        <tr>
                            <td><?= (int) $account['id'] ?></td>
                            <td><?= h($account['name']) ?></td>
                            <td><?= h($account['email']) ?></td>
                            <td><?= h($account['phone'] ?? '-') ?></td>
                            <td><span class="status-pill"><?= h(ucfirst($account['role'])) ?></span></td>
                            <td><?= h(date('M d, Y', strtotime($account['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section id="orders-view" class="admin-panel-section <?= $activeSection === 'orders' ? 'active' : '' ?>">
            <div class="table-header">
                <h2>Recent Orders</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= h($order['order_number']) ?></td>
                            <td>
                                <strong><?= h($order['customer_name']) ?></strong>
                                <span class="table-subtext"><?= h($order['shipping_email']) ?></span>
                            </td>
                            <td><?= h(date('M d, Y', strtotime($order['created_at']))) ?></td>
                            <td><?= format_price($order['total']) ?></td>
                            <td>
                                <form method="post" class="status-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="admin_action" value="update_order_status">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <select name="status" class="form-input" onchange="this.form.submit()">
                                        <?php foreach (['processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                                            <option value="<?= h($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= h(ucfirst($status)) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section id="messages-view" class="admin-panel-section <?= $activeSection === 'messages' ? 'active' : '' ?>">
            <div class="table-header">
                <h2>Contact Messages</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= h(date('M d, Y', strtotime($message['created_at']))) ?></td>
                            <td>
                                <strong><?= h($message['name']) ?></strong>
                                <span class="table-subtext"><?= h($message['email']) ?></span>
                                <?php if (!empty($message['phone'])): ?>
                                    <span class="table-subtext"><?= h($message['phone']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= h($message['subject']) ?></td>
                            <td class="message-cell"><?= h($message['message']) ?></td>
                            <td><span class="status-pill <?= $message['status'] === 'new' ? 'status-live' : '' ?>"><?= h(ucfirst($message['status'])) ?></span></td>
                            <td>
                                <?php if ($message['status'] === 'new'): ?>
                                    <form method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="admin_action" value="mark_message_read">
                                        <input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>">
                                        <button class="action-btn action-edit" type="submit">Mark read</button>
                                    </form>
                                <?php else: ?>
                                    <span class="table-subtext">Done</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<div class="modal-overlay" id="productModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="productModalTitle">Add Product</h3>
            <button class="btn btn-secondary compact-btn" type="button" onclick="closeProductModal()">Close</button>
        </div>
        <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="admin_action" value="save_product">
            <input type="hidden" name="product_id" id="product_id">

            <div class="form-group">
                <label class="form-label" for="product_name">Product Name</label>
                <input type="text" id="product_name" name="name" class="form-input" required>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="form-group">
                    <label class="form-label" for="product_category">Category</label>
                    <select id="product_category" name="category" class="form-input" required>
                        <?php foreach (categories() as $slug => $label): ?>
                            <option value="<?= h($slug) ?>"><?= h($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="product_price">Price</label>
                    <input type="number" id="product_price" name="price" step="0.01" min="1" class="form-input" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="form-group">
                    <label class="form-label" for="product_stock">Stock</label>
                    <input type="number" id="product_stock" name="stock" min="0" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="product_tag">Tag</label>
                    <input type="text" id="product_tag" name="tag" class="form-input" placeholder="NEW, HOT, SALE">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="form-group">
                    <label class="form-label" for="product_brand">Brand</label>
                    <input type="text" id="product_brand" name="brand" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label" for="product_warranty">Warranty</label>
                    <input type="text" id="product_warranty" name="warranty" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="product_image">Image Path</label>
                <input type="text" id="product_image" name="image" class="form-input" placeholder="assets/images/product.png" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="product_description">Description</label>
                <textarea id="product_description" name="description" class="form-input" rows="4" required></textarea>
            </div>

            <div class="admin-check-row">
                <label><input type="checkbox" name="is_featured" id="product_featured"> Featured</label>
                <label><input type="checkbox" name="status" id="product_status" checked> Live</label>
            </div>

            <button type="submit" class="btn btn-primary form-submit">Save Product</button>
        </form>
    </div>
</div>

<script>
const menuItems = document.querySelectorAll('.admin-menu-item');
const sections = document.querySelectorAll('.admin-panel-section');
const modal = document.getElementById('productModal');
const modalTitle = document.getElementById('productModalTitle');

function showSection(target) {
    menuItems.forEach((item) => item.classList.toggle('active', item.dataset.target === target));
    sections.forEach((section) => section.classList.toggle('active', section.id === target));
}

menuItems.forEach((item) => {
    item.addEventListener('click', () => showSection(item.dataset.target));
});

document.querySelectorAll('[data-jump]').forEach((link) => {
    link.addEventListener('click', (event) => {
        event.preventDefault();
        showSection(link.dataset.jump);
    });
});

function openProductModal(button = null) {
    const form = modal.querySelector('form');
    form.reset();
    document.getElementById('product_id').value = '';
    document.getElementById('product_status').checked = true;
    modalTitle.textContent = 'Add Product';

    if (button) {
        modalTitle.textContent = 'Edit Product';
        document.getElementById('product_id').value = button.dataset.id || '';
        document.getElementById('product_name').value = button.dataset.name || '';
        document.getElementById('product_category').value = button.dataset.category || 'mice';
        document.getElementById('product_price').value = button.dataset.price || '';
        document.getElementById('product_stock').value = button.dataset.stock || '0';
        document.getElementById('product_image').value = button.dataset.image || '';
        document.getElementById('product_brand').value = button.dataset.brand || '';
        document.getElementById('product_warranty').value = button.dataset.warranty || '';
        document.getElementById('product_tag').value = button.dataset.tag || '';
        document.getElementById('product_description').value = button.dataset.description || '';
        document.getElementById('product_featured').checked = button.dataset.featured === '1';
        document.getElementById('product_status').checked = button.dataset.status === '1';
    }

    modal.style.display = 'flex';
}

function closeProductModal() {
    modal.style.display = 'none';
}

modal.addEventListener('click', (event) => {
    if (event.target === modal) {
        closeProductModal();
    }
});
</script>

</body>

</html>
