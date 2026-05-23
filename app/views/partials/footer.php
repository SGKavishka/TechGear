    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="index.php" class="logo">Tech<span>Gear</span></a>
                    <p>Gaming hardware, desk gear, and PC components selected for serious everyday use.</p>
                </div>
                <div class="footer-col">
                    <h4>Shop</h4>
                    <ul class="footer-links">
                        <li><a href="products.php">All Products</a></li>
                        <li><a href="products.php?category=mice">Gaming Mice</a></li>
                        <li><a href="products.php?category=keyboards">Keyboards</a></li>
                        <li><a href="products.php?category=components">PC Components</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Account</h4>
                    <ul class="footer-links">
                        <li><a href="cart.php">Cart</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="contact.php">Support</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legal</h4>
                    <ul class="footer-links">
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="privacy.php#terms">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; <?= date('Y') ?> TechGear E-Commerce. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="<?= h(asset_url('js/app.js')) ?>"></script>
</body>

</html>
