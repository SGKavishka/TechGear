<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$pageTitle = 'TechGear | High-Performance Gaming Gear';
$pageDescription = 'Premium gaming hardware, PC components, and desk accessories.';
$activePage = 'home';
$extraCss = ['css/index.css'];

$featuredProducts = get_products(['featured' => true, 'limit' => 4]);
$categoryImages = [
    'mice' => 'images/viper_ultimate_rgb.png',
    'keyboards' => 'images/mech_pro_tkl.png',
    'headsets' => 'images/aura_sync_headset.png',
    'components' => 'images/rtx_4080_super.png',
];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main>
    <section class="hero-slideshow">
        <div class="slides-container">
            <div class="slide active">
                <div class="slide-bg" style="background-image: url('<?= h(asset_url('images/slide1.png')) ?>');"></div>
                <div class="container hero-content">
                    <span class="slide-badge">New Arrival</span>
                    <h1>Build a faster gaming setup</h1>
                    <p>Premium peripherals, PC parts, and streaming tools selected for precision, comfort, and reliable daily performance.</p>
                    <div class="flex gap-2 hero-actions">
                        <a href="products.php" class="btn btn-primary">Shop Now</a>
                        <a href="#featured" class="btn btn-secondary">Featured Gear</a>
                    </div>
                </div>
            </div>

            <div class="slide">
                <div class="slide-bg" style="background-image: url('<?= h(asset_url('images/slide2.png')) ?>');"></div>
                <div class="container hero-content">
                    <span class="slide-badge">Restocked</span>
                    <h1>4K-ready PC components</h1>
                    <p>Upgrade your build with high-end graphics, processors, and parts that keep demanding games smooth.</p>
                    <div class="flex gap-2 hero-actions">
                        <a href="product-detail.php?id=4" class="btn btn-primary">View RTX 4080</a>
                    </div>
                </div>
            </div>

            <div class="slide">
                <div class="slide-bg" style="background-image: url('<?= h(asset_url('images/slide3.png')) ?>');"></div>
                <div class="container hero-content">
                    <span class="slide-badge">Studio Audio</span>
                    <h1>Hear more, react sooner</h1>
                    <p>Clean headset audio, clear microphones, and focused accessories for competitive play and streaming.</p>
                    <div class="flex gap-2 hero-actions">
                        <a href="products.php?category=headsets" class="btn btn-primary">Shop Audio</a>
                    </div>
                </div>
            </div>

            <div class="slide">
                <div class="slide-bg" style="background-image: url('<?= h(asset_url('images/slide4.png')) ?>');"></div>
                <div class="container hero-content">
                    <span class="slide-badge">Top Rated</span>
                    <h1>Mechanical control that lasts</h1>
                    <p>Responsive keyboards with sturdy frames, clean switch feel, and layouts built for work and play.</p>
                    <div class="flex gap-2 hero-actions">
                        <a href="products.php?category=keyboards" class="btn btn-primary">Shop Keyboards</a>
                    </div>
                </div>
            </div>
        </div>

        <button class="slide-nav prev-slide" aria-label="Previous Slide">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <button class="slide-nav next-slide" aria-label="Next Slide">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <div class="slide-pagination">
            <span class="dot active" data-index="0"></span>
            <span class="dot" data-index="1"></span>
            <span class="dot" data-index="2"></span>
            <span class="dot" data-index="3"></span>
        </div>
    </section>

    <section class="container features-grid grid grid-cols-3 gap-3">
        <div class="glass-panel feature-card">
            <span class="feature-icon">01</span>
            <h3>Fast Dispatch</h3>
            <p>Orders are prepared quickly from available local stock.</p>
        </div>
        <div class="glass-panel feature-card">
            <span class="feature-icon">02</span>
            <h3>Verified Gear</h3>
            <p>Every item is listed with clear stock, warranty, and brand details.</p>
        </div>
        <div class="glass-panel feature-card">
            <span class="feature-icon">03</span>
            <h3>Real Support</h3>
            <p>Contact requests are stored in the admin panel for follow-up.</p>
        </div>
    </section>

    <section class="section-padding container">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Catalog</span>
                <h2>Browse By Category</h2>
            </div>
            <a href="products.php" class="btn btn-secondary">View All</a>
        </div>

        <div class="grid grid-cols-4 gap-3">
            <?php foreach ($categoryImages as $slug => $image): ?>
                <a href="products.php?category=<?= h($slug) ?>" class="category-card" style="background-image: url('<?= h(asset_url($image)) ?>');">
                    <div class="category-title"><?= h(category_label($slug)) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="featured" class="section-padding container">
        <div class="section-heading centered">
            <div>
                <span class="eyebrow">Featured</span>
                <h2>Recommended Gear</h2>
                <p>Popular hardware currently available in the TechGear catalog.</p>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <?php foreach ($featuredProducts as $product): ?>
                <article class="product-card">
                    <?php if (!empty($product['tag'])): ?>
                        <span class="product-badge"><?= h($product['tag']) ?></span>
                    <?php endif; ?>
                    <a class="product-link" href="product-detail.php?id=<?= (int) $product['id'] ?>">
                        <img src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?>" class="product-image">
                        <div class="product-info">
                            <span class="product-category"><?= h(category_label($product['category'])) ?></span>
                            <h3 class="product-title"><?= h($product['name']) ?></h3>
                            <div class="product-price"><?= format_price($product['price']) ?></div>
                        </div>
                    </a>
                    <form method="post" action="cart_action.php" class="product-action">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="redirect" value="index.php#featured">
                        <button class="btn btn-glass" type="submit">Add to Cart</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section id="newsletter" class="section-padding container">
        <div class="newsletter-panel">
            <span class="eyebrow">Member Updates</span>
            <h2>Get product drops and restock alerts</h2>
            <form method="post" action="subscribe.php" class="newsletter-form">
                <?= csrf_field() ?>
                <input type="email" name="email" class="form-input" placeholder="Enter your email address" required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </section>
</main>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
