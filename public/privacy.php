<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$pageTitle = 'Privacy Policy | TechGear';
$pageDescription = 'Privacy Policy and Terms of Service for TechGear.';
$activePage = '';
$extraCss = ['css/privacy.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="container section-padding">
    <div class="page-intro">
        <span class="eyebrow">Policy</span>
        <h1>Privacy & Terms</h1>
        <p>Last updated: May 2026</p>
    </div>

    <div class="document-container">
        <section class="doc-section">
            <h2>1. Information We Collect</h2>
            <p>TechGear collects account, order, delivery, and support information needed to run the store and respond to customers.</p>
            <ul>
                <li>Name, email address, phone number, and delivery address when you register or checkout.</li>
                <li>Order details, selected products, quantities, and delivery status.</li>
                <li>Contact form messages and newsletter subscription emails.</li>
            </ul>
        </section>

        <section class="doc-section">
            <h2>2. How We Use Your Information</h2>
            <p>We use your information to process orders, provide support, update account details, and improve product availability.</p>
            <ul>
                <li>To create accounts and authenticate users.</li>
                <li>To calculate cart totals and save order history.</li>
                <li>To let administrators manage inventory, orders, and support messages.</li>
            </ul>
        </section>

        <section id="terms" class="doc-section">
            <h2>3. Terms & Conditions of Sale</h2>
            <p>Product availability and pricing may change based on supplier stock. Returns and warranty claims are handled according to the warranty listed on the product page.</p>
            <ul>
                <li>Orders are accepted when the checkout flow creates an order record.</li>
                <li>Admins may update order status as processing, shipped, delivered, or cancelled.</li>
                <li>Payment gateway integration is not included in this local XAMPP project.</li>
            </ul>
        </section>

        <section class="doc-section">
            <h2>4. Data Security</h2>
            <p>Passwords are hashed with PHP password hashing. Forms use CSRF tokens, and database operations use prepared statements.</p>
        </section>
    </div>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
