<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$user = require_login();
$errors = [];
$activeTab = $_GET['tab'] ?? 'profile';
$activeTab = in_array($activeTab, ['profile', 'orders', 'tracking'], true) ? $activeTab : 'profile';

$form = [
    'name' => trim($_POST['name'] ?? ($user['name'] ?? '')),
    'phone' => trim($_POST['phone'] ?? ($user['phone'] ?? '')),
    'address' => trim($_POST['address'] ?? ($user['address'] ?? '')),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $password = $_POST['password'] ?? '';

    if (strlen($form['name']) < 3) {
        $errors['name'] = 'Full name must be at least 3 characters.';
    }

    if ($password !== '' && strlen($password) < 8) {
        $errors['password'] = 'New password must be at least 8 characters.';
    }

    if (!$errors) {
        if ($password !== '') {
            $stmt = db()->prepare('UPDATE users SET name = ?, phone = ?, address = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([
                $form['name'],
                $form['phone'] ?: null,
                $form['address'] ?: null,
                password_hash($password, PASSWORD_DEFAULT),
                (int) $user['id'],
            ]);
        } else {
            $stmt = db()->prepare('UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?');
            $stmt->execute([
                $form['name'],
                $form['phone'] ?: null,
                $form['address'] ?: null,
                (int) $user['id'],
            ]);
        }

        flash('success', 'Profile updated.');
        redirect('profile.php');
    }
}

$ordersStmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$ordersStmt->execute([(int) $user['id']]);
$orders = $ordersStmt->fetchAll();
$itemsByOrder = [];

if ($orders) {
    $ids = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $itemsStmt = db()->prepare("SELECT * FROM order_items WHERE order_id IN ($placeholders) ORDER BY id ASC");
    $itemsStmt->execute(array_map('intval', $ids));
    foreach ($itemsStmt->fetchAll() as $item) {
        $itemsByOrder[(int) $item['order_id']][] = $item;
    }
}

$activeOrders = array_values(array_filter($orders, fn(array $order): bool => !in_array($order['status'], ['delivered', 'cancelled'], true)));

$pageTitle = 'Dashboard | TechGear';
$pageDescription = 'Manage your TechGear account profile.';
$activePage = 'profile';
$extraCss = ['css/profile.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="container section-padding">
    <div class="dashboard-container">
        <aside class="dashboard-nav">
            <div class="user-widget">
                <div class="user-avatar"><?= h(strtoupper(substr($user['name'], 0, 1))) ?></div>
                <div class="user-name"><?= h($user['name']) ?></div>
                <div class="user-email"><?= h($user['email']) ?></div>
            </div>

            <nav class="dashboard-menu">
                <button class="menu-item <?= $activeTab === 'profile' ? 'active' : '' ?>" data-target="profile-edit" type="button">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile Settings
                </button>
                <button class="menu-item <?= $activeTab === 'orders' ? 'active' : '' ?>" data-target="order-history" type="button">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Purchase History
                </button>
                <button class="menu-item <?= $activeTab === 'tracking' ? 'active' : '' ?>" data-target="order-tracking" type="button">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        <path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293L21 11"></path>
                    </svg>
                    Order Tracking
                </button>
                <a class="menu-item logout-menu-item" href="logout.php">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M10 17l5-5-5-5"></path>
                        <path d="M15 12H3"></path>
                        <path d="M21 3v18"></path>
                    </svg>
                    Logout
                </a>
            </nav>
        </aside>

        <div class="dashboard-content">
            <section id="profile-edit" class="content-section <?= $activeTab === 'profile' ? 'active' : '' ?>">
                <div class="section-header">
                    <h2>Update Account</h2>
                </div>
                <form method="post" novalidate>
                    <?= csrf_field() ?>
                    <div class="grid grid-cols-2 gap-3 profile-grid">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" value="<?= h($form['name']) ?>">
                            <?php if (isset($errors['name'])): ?><span class="error-message visible"><?= h($errors['name']) ?></span><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" class="form-input" value="<?= h($user['email']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="form-input" value="<?= h($form['phone']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="address">Shipping Address</label>
                            <input type="text" id="address" name="address" class="form-input" value="<?= h($form['address']) ?>">
                        </div>
                        <div class="form-group full-span">
                            <label class="form-label" for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-input <?= isset($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Leave blank to keep current password">
                            <?php if (isset($errors['password'])): ?><span class="error-message visible"><?= h($errors['password']) ?></span><?php endif; ?>
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </form>
            </section>

            <section id="order-history" class="content-section <?= $activeTab === 'orders' ? 'active' : '' ?>">
                <div class="section-header">
                    <h2>Purchase History</h2>
                </div>

                <?php if (!$orders): ?>
                    <div class="empty-panel">
                        <h3>No orders yet</h3>
                        <p>Your completed checkout orders will appear here.</p>
                        <a href="products.php" class="btn btn-secondary">Browse Products</a>
                    </div>
                <?php endif; ?>

                <?php foreach ($orders as $order): ?>
                    <article class="order-card">
                        <div>
                            <span class="order-status <?= h(order_status_class($order['status'])) ?>"><?= h(ucfirst($order['status'])) ?></span>
                            <div class="order-meta"><?= h($order['order_number']) ?> | <?= h(date('M d, Y', strtotime($order['created_at']))) ?></div>
                        </div>
                        <div>
                            <h4><?= count($itemsByOrder[(int) $order['id']] ?? []) ?> item<?= count($itemsByOrder[(int) $order['id']] ?? []) === 1 ? '' : 's' ?></h4>
                            <div class="order-items">
                                <?php foreach (($itemsByOrder[(int) $order['id']] ?? []) as $item): ?>
                                    <span><?= h($item['product_name']) ?> x <?= (int) $item['quantity'] ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="order-meta"><?= format_price($order['total']) ?></div>
                        </div>
                        <a class="btn btn-secondary" href="products.php">Buy Again</a>
                    </article>
                <?php endforeach; ?>
            </section>

            <section id="order-tracking" class="content-section <?= $activeTab === 'tracking' ? 'active' : '' ?>">
                <div class="section-header">
                    <h2>Active Deliveries</h2>
                </div>

                <?php if (!$activeOrders): ?>
                    <div class="empty-panel">
                        <h3>No active deliveries</h3>
                        <p>Orders currently in processing or shipped status will show here.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($activeOrders as $order): ?>
                    <article class="order-card tracking-card">
                        <div>
                            <span class="order-status <?= h(order_status_class($order['status'])) ?>"><?= h(ucfirst($order['status'])) ?></span>
                            <div class="order-meta"><?= h($order['order_number']) ?> | <?= h(date('M d, Y', strtotime($order['created_at']))) ?></div>
                        </div>
                        <div>
                            <h4>Delivery to <?= h($order['shipping_name']) ?></h4>
                            <div class="order-meta"><?= h($order['shipping_address']) ?></div>
                            <div class="progress-track">
                                <span class="active"></span>
                                <span class="<?= in_array($order['status'], ['shipped', 'delivered'], true) ? 'active' : '' ?>"></span>
                                <span class="<?= $order['status'] === 'delivered' ? 'active' : '' ?>"></span>
                            </div>
                            <div class="progress-labels">
                                <span>Processing</span>
                                <span>Shipped</span>
                                <span>Delivered</span>
                            </div>
                        </div>
                        <span class="order-total"><?= format_price($order['total']) ?></span>
                    </article>
                <?php endforeach; ?>
            </section>
        </div>
    </div>
</main>

<script>
document.querySelectorAll('.menu-item[data-target]').forEach((item) => {
    item.addEventListener('click', () => {
        document.querySelectorAll('.menu-item[data-target]').forEach((button) => button.classList.remove('active'));
        document.querySelectorAll('.content-section').forEach((section) => section.classList.remove('active'));
        item.classList.add('active');
        document.getElementById(item.dataset.target).classList.add('active');
    });
});
</script>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
