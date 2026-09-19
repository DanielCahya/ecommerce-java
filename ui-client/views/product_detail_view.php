<?php

/**
 * Expects $product array with keys:
 * id, name, description, price, stock, category
 * Optional implicit fields: sale, premium, countdown
 */
?>

<div class="product-detail-card <?php echo ($product['sale'] ?? false) ? 'sale' : ''; ?> <?php echo ($product['premium'] ?? false) ? 'premium' : ''; ?>">
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
    <p><strong>Price:</strong> Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
    <p><strong>Stock:</strong> <?php echo htmlspecialchars($product['stock']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

    <?php if(isset($product['countdown'])): ?>
        <p class="countdown" data-seconds="<?php echo (int)$product['countdown']; ?>"></p>
    <?php endif; ?>

    <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn-detail">Add to Cart</a>

    <?php if(isset($product['sale']) && $product['sale']): ?>
        <span class="badge sale-badge">Sale!</span>
    <?php endif; ?>

    <?php if(isset($product['premium']) && $product['premium']): ?>
        <span class="badge premium-badge">Premium</span>
    <?php endif; ?>
</div>
