<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/core/init.php';

$productId = (int) ($_GET['id'] ?? 0);
$product = $productId > 0 ? get_product($productId) : null;

$pageTitle = $product ? $product['name'] . ' | TechGear' : 'Product Not Found | TechGear';
$pageDescription = $product['description'] ?? 'View product details on TechGear.';
$activePage = 'products';
$extraCss = ['css/product-detail.css'];

?>
<?php require APP_PATH . '/views/partials/header.php'; ?>

<main class="container section-padding">
    <?php if (!$product): ?>
        <div id="error-state" style="display:block;">
            <h1>Product Not Found</h1>
            <p>The item you are looking for does not exist or has been removed.</p>
            <a href="products.php" class="btn btn-primary">Return to Catalog</a>
        </div>
    <?php else: ?>
        <div class="product-detail-container">
            <div class="image-gallery">
                <img src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?>" class="main-image">
                <div class="thumbnail-list">
                    <img class="thumbnail active" src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?> thumbnail">
                    <img class="thumbnail" src="<?= h(product_image_url($product['image'])) ?>" alt="<?= h($product['name']) ?> thumbnail">
                </div>
            </div>

            <div class="product-info-details">
                <span class="category"><?= h(category_label($product['category'])) ?></span>
                <h1><?= h($product['name']) ?></h1>
                <div class="price"><?= format_price($product['price']) ?></div>

                <p class="description"><?= h($product['description']) ?></p>

                <form method="post" action="cart_action.php" class="action-area">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="redirect" value="product-detail.php?id=<?= (int) $product['id'] ?>">
                    <div class="quantity-control">
                        <button class="qty-btn" type="button" data-step="-1">-</button>
                        <input type="number" name="quantity" class="qty-input" value="1" min="1" max="10">
                        <button class="qty-btn" type="button" data-step="1">+</button>
                    </div>
                    <button class="btn btn-primary add-to-cart-btn" type="submit">Add to Cart</button>
                </form>

                <h3>Technical Specifications</h3>
                <table class="specs-table">
                    <tbody>
                        <tr>
                            <th>Brand</th>
                            <td><?= h($product['brand']) ?></td>
                        </tr>
                        <tr>
                            <th>Condition</th>
                            <td>Brand New</td>
                        </tr>
                        <tr>
                            <th>Warranty</th>
                            <td><?= h($product['warranty']) ?></td>
                        </tr>
                        <tr>
                            <th>Availability</th>
                            <td><?= (int) $product['stock'] ?> units in stock</td>
                        </tr>
                        <tr>
                            <th>SKU</th>
                            <td>TG-<?= 1000 + (int) $product['id'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
document.querySelectorAll('.qty-btn').forEach((button) => {
    button.addEventListener('click', () => {
        const input = button.parentElement.querySelector('.qty-input');
        const step = Number(button.dataset.step);
        const next = Math.max(1, Math.min(10, Number(input.value || 1) + step));
        input.value = String(next);
    });
});
</script>

<?php require APP_PATH . '/views/partials/footer.php'; ?>
