<?php
$pageTitle = $pageTitle ?? APP_NAME;
$pageDescription = $pageDescription ?? 'Premium gaming hardware and PC accessories.';
$activePage = $activePage ?? '';
$extraCss = $extraCss ?? [];
$user = current_user();
$cartCount = cart_count();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= h($pageDescription) ?>">
    <title><?= h($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= h(asset_url('css/styles.css')) ?>">
    <link rel="stylesheet" href="<?= h(asset_url('css/components.css')) ?>">
    <?php foreach ($extraCss as $cssFile): ?>
        <link rel="stylesheet" href="<?= h(asset_url($cssFile)) ?>">
    <?php endforeach; ?>
</head>

<body>
    <header class="header">
        <div class="container nav-container">
            <a href="index.php" class="logo">Tech<span>Gear</span></a>

            <nav class="nav-links">
                <a href="index.php" class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>">Home</a>
                <a href="products.php" class="nav-link <?= $activePage === 'products' ? 'active' : '' ?>">Products</a>
                <a href="contact.php" class="nav-link <?= $activePage === 'contact' ? 'active' : '' ?>">Contact</a>
            </nav>

            <div class="nav-actions">
                <?php if (!$user): ?>
                    <a href="login.php" class="nav-login-button <?= $activePage === 'login' ? 'active' : '' ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 21a8 8 0 10-16 0"></path>
                            <path d="M12 13a5 5 0 100-10 5 5 0 000 10z"></path>
                        </svg>
                        <span>Login</span>
                    </a>
                <?php endif; ?>
                <a href="cart.php" class="cart-icon <?= $activePage === 'cart' ? 'active' : '' ?>" aria-label="Shopping Cart">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 20a1 1 0 100-2 1 1 0 000 2zM20 20a1 1 0 100-2 1 1 0 000 2zM1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"></path>
                    </svg>
                    <span class="cart-count" style="<?= $cartCount > 0 ? '' : 'display:none;' ?>"><?= $cartCount ?></span>
                </a>
                <?php if ($user): ?>
                    <a href="profile.php" class="account-button <?= $activePage === 'profile' ? 'active' : '' ?>" aria-label="Profile">
                        <svg class="account-button-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 21a8 8 0 10-16 0"></path>
                            <path d="M12 13a5 5 0 100-10 5 5 0 000 10z"></path>
                        </svg>
                        <span class="account-button-label">Profile</span>
                    </a>
                <?php endif; ?>
                <button class="mobile-toggle" type="button" aria-label="Open navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <?php if ($message = flash('success')): ?>
        <div class="flash-message flash-success"><?= h($message) ?></div>
    <?php endif; ?>
    <?php if ($message = flash('error')): ?>
        <div class="flash-message flash-error"><?= h($message) ?></div>
    <?php endif; ?>
