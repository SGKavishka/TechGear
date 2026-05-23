<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$user = require_login();
$lines = cart_lines();

if (!$lines) {
    flash('error', 'Your cart is empty.');
    redirect('cart.php');
}

$errors = [];
$form = [
    'name' => $user['name'] ?? '',
    'email' => $user['email'] ?? '',
    'phone' => $user['phone'] ?? '',
    'address' => $user['address'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $form = [
        'name' => trim($_POST['name'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ];

    if (strlen($form['name']) < 3) {
        $errors['name'] = 'Enter a valid full name.';
    }

    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if (strlen($form['phone']) < 7) {
        $errors['phone'] = 'Enter a valid phone number.';
    }

    if (strlen($form['address']) < 8) {
        $errors['address'] = 'Enter a complete delivery address.';
    }

    if (!$errors) {
        try {
            $orderId = create_order($user, $form);
            flash('success', 'Order placed successfully. Order ID #' . $orderId . ' is now processing.');
            redirect('profile.php?tab=orders');
        } catch (Throwable $e) {
            $errors['general'] = $e->getMessage();
        }
    }
}

$totals = cart_totals($lines);

$pageTitle = 'Checkout | TechGear';
$pageDescription = 'Complete your TechGear order.';
$activePage = 'cart';
$extraCss = ['css/cart.css', 'css/login.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="container section-padding">
    <div class="checkout-layout">
        <section class="glass-panel checkout-panel">
            <span class="eyebrow">Checkout</span>
            <h1>Delivery Details</h1>

            <?php if (!empty($errors['general'])): ?>
                <div class="notice danger"><?= h($errors['general']) ?></div>
            <?php endif; ?>

            <form method="post" novalidate>
                <?= csrf_field() ?>
                <div class="grid grid-cols-2 gap-3 checkout-grid">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input class="form-input <?= isset($errors['name']) ? 'is-invalid' : '' ?>" type="text" id="name" name="name" value="<?= h($form['name']) ?>">
                        <?php if (isset($errors['name'])): ?><span class="error-message visible"><?= h($errors['name']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input class="form-input <?= isset($errors['email']) ? 'is-invalid' : '' ?>" type="email" id="email" name="email" value="<?= h($form['email']) ?>">
                        <?php if (isset($errors['email'])): ?><span class="error-message visible"><?= h($errors['email']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone</label>
                        <input class="form-input <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" type="text" id="phone" name="phone" value="<?= h($form['phone']) ?>">
                        <?php if (isset($errors['phone'])): ?><span class="error-message visible"><?= h($errors['phone']) ?></span><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Delivery Address</label>
                        <input class="form-input <?= isset($errors['address']) ? 'is-invalid' : '' ?>" type="text" id="address" name="address" value="<?= h($form['address']) ?>">
                        <?php if (isset($errors['address'])): ?><span class="error-message visible"><?= h($errors['address']) ?></span><?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Place Order</button>
                <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
            </form>
        </section>

        <aside class="cart-section checkout-summary">
            <h3 class="cart-title">Order Summary</h3>
            <?php foreach ($lines as $line): ?>
                <?php $product = $line['product']; ?>
                <div class="checkout-line">
                    <img src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?>">
                    <div>
                        <strong><?= h($product['name']) ?></strong>
                        <span>Qty <?= (int) $line['quantity'] ?></span>
                    </div>
                    <b><?= format_price($line['line_total']) ?></b>
                </div>
            <?php endforeach; ?>

            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= format_price($totals['subtotal']) ?></span>
            </div>
            <div class="summary-row">
                <span>Tax</span>
                <span><?= format_price($totals['tax']) ?></span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span><?= format_price($totals['total']) ?></span>
            </div>
        </aside>
    </div>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
