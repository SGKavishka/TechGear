<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$pageTitle = 'Shopping Cart | TechGear';
$pageDescription = 'Review your TechGear shopping cart.';
$activePage = 'cart';
$extraCss = ['css/cart.css'];

$lines = cart_lines();
$totals = cart_totals($lines);

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="container section-padding">
    <div class="cart-layout">
        <section class="cart-section">
            <div class="cart-title-row">
                <h1 class="cart-title">Your Cart</h1>
                <?php if ($lines): ?>
                    <form method="post" action="cart_action.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="clear">
                        <input type="hidden" name="redirect" value="cart.php">
                        <button class="link-button danger-link" type="submit">Clear cart</button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (!$lines): ?>
                <div id="empty-cart" style="display:block;">
                    <svg width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    <h3>Your cart is empty</h3>
                    <p>Choose a product from the catalog to start an order.</p>
                    <a href="products.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div id="cart-items-container">
                    <?php foreach ($lines as $line): ?>
                        <?php $product = $line['product']; ?>
                        <div class="cart-item">
                            <img src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?>" class="item-image">

                            <div class="item-details">
                                <span class="item-category"><?= h(category_label($product['category'])) ?></span>
                                <a href="product-detail.php?id=<?= (int) $product['id'] ?>">
                                    <h4><?= h($product['name']) ?></h4>
                                </a>
                                <form method="post" action="cart_action.php">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <input type="hidden" name="redirect" value="cart.php">
                                    <button class="item-action-remove" type="submit">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Remove
                                    </button>
                                </form>
                            </div>

                            <form method="post" action="cart_action.php" class="item-qty">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="hidden" name="redirect" value="cart.php">
                                <button type="button" data-step="-1">-</button>
                                <input type="number" name="quantity" value="<?= (int) $line['quantity'] ?>" min="0" max="10" aria-label="Quantity">
                                <button type="button" data-step="1">+</button>
                            </form>

                            <div class="item-price"><?= format_price($line['line_total']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="cart-section" id="summary-section">
            <h3 class="cart-title">Order Summary</h3>

            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= format_price($totals['subtotal']) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            <div class="summary-row">
                <span>Tax (Estimated)</span>
                <span><?= format_price($totals['tax']) ?></span>
            </div>

            <div class="summary-total">
                <span>Total</span>
                <span><?= format_price($totals['total']) ?></span>
            </div>

            <?php if ($lines): ?>
                <a href="checkout.php" class="btn btn-primary checkout-btn">Proceed to Checkout</a>
            <?php else: ?>
                <button class="btn btn-primary checkout-btn" type="button" disabled>Proceed to Checkout</button>
            <?php endif; ?>
        </aside>
    </div>
</main>

<script>
document.querySelectorAll('.item-qty').forEach((form) => {
    const input = form.querySelector('input[name="quantity"]');
    form.querySelectorAll('button[data-step]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = Math.max(0, Math.min(10, Number(input.value || 0) + Number(button.dataset.step)));
            input.value = String(next);
            form.submit();
        });
    });
});
</script>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
