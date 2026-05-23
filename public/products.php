<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$pageTitle = 'Products | TechGear';
$pageDescription = 'Browse premium gaming peripherals, PC components, and accessories.';
$activePage = 'products';
$extraCss = ['css/products.css'];

$selectedCategory = $_GET['category'] ?? 'all';
$selectedPrice = $_GET['price'] ?? 'all';
$search = trim($_GET['search'] ?? '');

if ($selectedCategory !== 'all' && !array_key_exists($selectedCategory, categories())) {
    $selectedCategory = 'all';
}

if (!in_array($selectedPrice, ['all', 'under50000', '50000to150000', 'over150000'], true)) {
    $selectedPrice = 'all';
}

$products = get_products([
    'category' => $selectedCategory,
    'price' => $selectedPrice,
    'search' => $search,
]);

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="section-padding container">
    <div class="catalog-container">
        <aside class="sidebar">
            <form method="get" id="catalogFilterForm">
                <div class="filter-section">
                    <h3 class="filter-title">Categories</h3>
                    <div class="filter-list">
                        <label class="filter-item">
                            <input type="radio" name="category" value="all" <?= $selectedCategory === 'all' ? 'checked' : '' ?>>
                            <span>All Products</span>
                        </label>
                        <?php foreach (categories() as $slug => $label): ?>
                            <label class="filter-item">
                                <input type="radio" name="category" value="<?= h($slug) ?>" <?= $selectedCategory === $slug ? 'checked' : '' ?>>
                                <span><?= h($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Price Range</h3>
                    <div class="filter-list">
                        <label class="filter-item">
                            <input type="radio" name="price" value="all" <?= $selectedPrice === 'all' ? 'checked' : '' ?>>
                            <span>Any Price</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="price" value="under50000" <?= $selectedPrice === 'under50000' ? 'checked' : '' ?>>
                            <span>Under Rs. 50,000</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="price" value="50000to150000" <?= $selectedPrice === '50000to150000' ? 'checked' : '' ?>>
                            <span>Rs. 50,000 - Rs. 150,000</span>
                        </label>
                        <label class="filter-item">
                            <input type="radio" name="price" value="over150000" <?= $selectedPrice === 'over150000' ? 'checked' : '' ?>>
                            <span>Over Rs. 150,000</span>
                        </label>
                    </div>
                </div>

                <input type="hidden" name="search" value="<?= h($search) ?>">
                <button type="submit" class="btn btn-secondary filter-submit">Apply Filters</button>
            </form>
        </aside>

        <section>
            <form method="get" class="catalog-header">
                <div>
                    <span class="eyebrow">Store</span>
                    <h1>Product Catalog</h1>
                    <p><?= count($products) ?> item<?= count($products) === 1 ? '' : 's' ?> found</p>
                </div>

                <input type="hidden" name="category" value="<?= h($selectedCategory) ?>">
                <input type="hidden" name="price" value="<?= h($selectedPrice) ?>">
                <div class="search-bar">
                    <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="<?= h($search) ?>" placeholder="Search products...">
                </div>
                <button class="btn btn-primary catalog-search-btn" type="submit">Search</button>
            </form>

            <?php if (!$products): ?>
                <div id="empty-state" style="display:block;">
                    <h3>No products found</h3>
                    <p>Try another category, search term, or price range.</p>
                    <a href="products.php" class="btn btn-secondary">Reset Filters</a>
                </div>
            <?php else: ?>
                <div id="product-grid" class="grid grid-cols-3 gap-3">
                    <?php foreach ($products as $product): ?>
                        <article class="product-card">
                            <?php if (!empty($product['tag'])): ?>
                                <span class="product-badge"><?= h($product['tag']) ?></span>
                            <?php endif; ?>
                            <a class="product-link" href="product-detail.php?id=<?= (int) $product['id'] ?>">
                                <img src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?>" class="product-image">
                                <div class="product-info">
                                    <span class="product-category"><?= h(category_label($product['category'])) ?></span>
                                    <h3 class="product-title"><?= h($product['name']) ?></h3>
                                    <p class="product-excerpt"><?= h($product['description']) ?></p>
                                    <div class="product-meta-row">
                                        <span><?= (int) $product['stock'] ?> in stock</span>
                                    </div>
                                    <div class="product-price"><?= format_price($product['price']) ?></div>
                                </div>
                            </a>
                            <form method="post" action="cart_action.php" class="product-action">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI'] ?? 'products.php') ?>">
                                <button class="btn btn-glass" type="submit">Add to Cart</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>

<script>
document.querySelectorAll('#catalogFilterForm input[type="radio"]').forEach((input) => {
    input.addEventListener('change', () => input.form.submit());
});
</script>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
