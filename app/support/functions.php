<?php
declare(strict_types=1);

require_once __DIR__ . '/../core/database.php';

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function asset_url(string $path): string
{
    $path = ltrim($path, '/');

    if (preg_match('/^(https?:)?\/\//i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }

    if (!str_starts_with($path, 'assets/')) {
        $path = 'assets/' . $path;
    }

    if (preg_match('#^assets/(css|js)/#', $path)) {
        $file = PUBLIC_PATH . '/' . $path;
        if (is_file($file)) {
            return $path . '?v=' . filemtime($file);
        }
    }

    return $path;
}

function product_image_url(?string $path): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return asset_url('images/placeholder.svg');
    }

    return asset_url($path);
}

function safe_redirect_target(?string $target, string $fallback = 'index.php'): string
{
    if (!$target || str_contains($target, "\n") || str_contains($target, "\r")) {
        return $fallback;
    }

    if (preg_match('/^https?:\/\//i', $target)) {
        return $fallback;
    }

    return $target;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $value;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid request token.');
    }
}

function format_price(float|int|string $amount): string
{
    return 'Rs. ' . number_format((float) $amount, 2);
}

function category_label(string $category): string
{
    return [
        'mice' => 'Gaming Mice',
        'keyboards' => 'Keyboards',
        'headsets' => 'Headsets',
        'components' => 'PC Components',
        'accessories' => 'Accessories',
    ][$category] ?? ucfirst($category);
}

function categories(): array
{
    return [
        'mice' => 'Gaming Mice',
        'keyboards' => 'Keyboards',
        'headsets' => 'Headsets',
        'components' => 'PC Components',
        'accessories' => 'Accessories',
    ];
}

function current_user(): ?array
{
    static $user = false;

    if ($user !== false) {
        return $user;
    }

    if (empty($_SESSION['user_id'])) {
        $user = null;
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email, role, phone, address, created_at FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $_SESSION['user_id']]);
    $user = $stmt->fetch() ?: null;

    if (!$user) {
        unset($_SESSION['user_id']);
    }

    return $user;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        flash('error', 'Please log in to continue.');
        redirect('login.php?next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? 'profile.php'));
    }

    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if (($user['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Forbidden');
    }

    return $user;
}

function current_admin(): ?array
{
    static $admin = false;

    if ($admin !== false) {
        return $admin;
    }

    if (empty($_SESSION['admin_user_id'])) {
        $admin = null;
        return null;
    }

    $stmt = db()->prepare("SELECT id, name, email, role, phone, address, created_at FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
    $stmt->execute([(int) $_SESSION['admin_user_id']]);
    $admin = $stmt->fetch() ?: null;

    if (!$admin) {
        unset($_SESSION['admin_user_id']);
    }

    return $admin;
}

function require_admin_portal(): array
{
    $admin = current_admin();
    if (!$admin) {
        flash('error', 'Please sign in through the admin portal.');
        redirect('admin-login.php');
    }

    return $admin;
}

function get_products(array $filters = []): array
{
    $where = ['status = 1'];
    $params = [];

    if (!empty($filters['category']) && $filters['category'] !== 'all') {
        $where[] = 'category = ?';
        $params[] = $filters['category'];
    }

    if (!empty($filters['search'])) {
        $where[] = '(name LIKE ? OR description LIKE ?)';
        $needle = '%' . $filters['search'] . '%';
        $params[] = $needle;
        $params[] = $needle;
    }

    if (!empty($filters['price'])) {
        if ($filters['price'] === 'under50000') {
            $where[] = 'price < 50000';
        } elseif ($filters['price'] === '50000to150000') {
            $where[] = 'price BETWEEN 50000 AND 150000';
        } elseif ($filters['price'] === 'over150000') {
            $where[] = 'price > 150000';
        }
    }

    if (isset($filters['featured'])) {
        $where[] = 'is_featured = ?';
        $params[] = (int) (bool) $filters['featured'];
    }

    $sql = 'SELECT * FROM products WHERE ' . implode(' AND ', $where) . ' ORDER BY is_featured DESC, id ASC';

    if (!empty($filters['limit'])) {
        $sql .= ' LIMIT ' . max(1, (int) $filters['limit']);
    }

    $stmt = db()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

function get_product(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? AND status = 1 LIMIT 1');
    $stmt->execute([$id]);

    return $stmt->fetch() ?: null;
}

function get_product_for_admin(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);

    return $stmt->fetch() ?: null;
}

function cart_get(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_set(array $cart): void
{
    $clean = [];

    foreach ($cart as $productId => $quantity) {
        $productId = (int) $productId;
        $quantity = max(1, min(10, (int) $quantity));

        if ($productId > 0) {
            $clean[$productId] = $quantity;
        }
    }

    $_SESSION['cart'] = $clean;
}

function cart_count(): int
{
    return array_sum(array_map('intval', cart_get()));
}

function cart_add(int $productId, int $quantity = 1): void
{
    $cart = cart_get();
    $cart[$productId] = min(10, ($cart[$productId] ?? 0) + max(1, $quantity));
    cart_set($cart);
}

function cart_update(int $productId, int $quantity): void
{
    $cart = cart_get();

    if ($quantity <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = min(10, $quantity);
    }

    cart_set($cart);
}

function cart_remove(int $productId): void
{
    $cart = cart_get();
    unset($cart[$productId]);
    cart_set($cart);
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}

function cart_lines(): array
{
    $cart = cart_get();
    if (!$cart) {
        return [];
    }

    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = db()->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND status = 1");
    $stmt->execute($ids);
    $products = [];

    foreach ($stmt->fetchAll() as $product) {
        $products[(int) $product['id']] = $product;
    }

    $lines = [];
    foreach ($cart as $productId => $quantity) {
        $productId = (int) $productId;
        if (!isset($products[$productId])) {
            continue;
        }

        $quantity = (int) $quantity;
        $lineTotal = (float) $products[$productId]['price'] * $quantity;
        $lines[] = [
            'product' => $products[$productId],
            'quantity' => $quantity,
            'line_total' => $lineTotal,
        ];
    }

    return $lines;
}

function cart_totals(?array $lines = null): array
{
    $lines ??= cart_lines();
    $subtotal = 0.0;

    foreach ($lines as $line) {
        $subtotal += (float) $line['line_total'];
    }

    $tax = $subtotal * TAX_RATE;
    $shipping = 0.0;

    return [
        'subtotal' => $subtotal,
        'tax' => $tax,
        'shipping' => $shipping,
        'total' => $subtotal + $tax + $shipping,
    ];
}

function order_status_class(string $status): string
{
    return [
        'processing' => 'status-processing',
        'shipped' => 'status-processing',
        'delivered' => 'status-delivered',
        'cancelled' => 'status-cancelled',
    ][$status] ?? 'status-processing';
}

function generate_order_number(): string
{
    do {
        $number = 'TG-' . date('ymd') . '-' . random_int(1000, 9999);
        $stmt = db()->prepare('SELECT id FROM orders WHERE order_number = ? LIMIT 1');
        $stmt->execute([$number]);
    } while ($stmt->fetch());

    return $number;
}

function create_order(array $user, array $shipping): int
{
    $lines = cart_lines();
    if (!$lines) {
        throw new RuntimeException('Your cart is empty.');
    }

    $totals = cart_totals($lines);
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare(
            'INSERT INTO orders
            (user_id, order_number, subtotal, tax, shipping, total, status, shipping_name, shipping_email, shipping_phone, shipping_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            (int) $user['id'],
            generate_order_number(),
            $totals['subtotal'],
            $totals['tax'],
            $totals['shipping'],
            $totals['total'],
            'processing',
            $shipping['name'],
            $shipping['email'],
            $shipping['phone'],
            $shipping['address'],
        ]);

        $orderId = (int) $pdo->lastInsertId();
        $itemStmt = $pdo->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, price, quantity, image)
            VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stockStmt = $pdo->prepare('UPDATE products SET stock = GREATEST(stock - ?, 0) WHERE id = ?');

        foreach ($lines as $line) {
            $product = $line['product'];
            $itemStmt->execute([
                $orderId,
                (int) $product['id'],
                $product['name'],
                (float) $product['price'],
                (int) $line['quantity'],
                $product['image'],
            ]);
            $stockStmt->execute([(int) $line['quantity'], (int) $product['id']]);
        }

        $pdo->commit();
        cart_clear();

        return $orderId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}
