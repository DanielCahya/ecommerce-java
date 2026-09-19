<div class="product-list">
    <?php if (empty($products)): ?>
        <p>No products available.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card <?php echo $product['ui_theme'] ?? ''; ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="price"><?php echo htmlspecialchars($product['formatted_price']); ?></p>
                <p class="stock">Stock: <?php echo $product['stock']; ?></p>

                <?php if (!empty($product['ui_badge'])): ?>
                    <span class="badge"><?php echo htmlspecialchars($product['ui_badge']); ?></span>
                <?php endif; ?>

                <?php if (!empty($product['ui_recommendation'])): ?>
                    <p class="recommendation"><?php echo htmlspecialchars($product['ui_recommendation']); ?></p>
                <?php endif; ?>

                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-detail">View Details</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
