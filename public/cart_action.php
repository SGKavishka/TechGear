<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('cart.php');
}

verify_csrf();

$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = (int) ($_POST['quantity'] ?? 1);

if ($productId <= 0 && $action !== 'clear') {
    flash('error', 'Invalid product selected.');
    redirect('products.php');
}

if ($action === 'add') {
    $product = get_product($productId);
    if (!$product) {
        flash('error', 'That product is no longer available.');
        redirect('products.php');
    }

    cart_add($productId, $quantity);
    flash('success', $product['name'] . ' added to cart.');
} elseif ($action === 'update') {
    cart_update($productId, $quantity);
    flash('success', 'Cart updated.');
} elseif ($action === 'remove') {
    cart_remove($productId);
    flash('success', 'Item removed from cart.');
} elseif ($action === 'clear') {
    cart_clear();
    flash('success', 'Cart cleared.');
}

$target = safe_redirect_target($_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? 'cart.php'), 'cart.php');
redirect($target);
