<?php
//  Checkout Page

// Load required components
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/helpers.php';

// Restrict access to logged-in users only
require_login();

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: ' . base_url('/cart.php'));
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Lock products for update
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $ph  = implode(',', $ids);

    $stmt = $pdo->query("SELECT * FROM products WHERE product_id IN ($ph) FOR UPDATE");
    $products = [];
    while ($row = $stmt->fetch()) {
        $products[$row['product_id']] = $row;
    }

    // Validate cart and calculate total
    $total = 0.00;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        if (!isset($products[$pid])) {
            throw new Exception("Product not found.");
        }
        if ($products[$pid]['quantity'] < $qty) {
            throw new Exception("Insufficient stock for: " . $products[$pid]['name']);
        }
        $total += $qty * (float)$products[$pid]['cost'];
    }

    // Insert order record
    $insertOrder = $pdo->prepare("
        INSERT INTO orders (user_id, total_amount, status) 
        VALUES (?, ?, 'paid')
    ");
    $insertOrder->execute([$_SESSION['user_id'], $total]);
    $order_id = (int)$pdo->lastInsertId();

    // Insert order items and update stock
    $insertItem = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
    $updateStock = $pdo->prepare("
        UPDATE products 
        SET quantity = quantity - ? 
        WHERE product_id = ?
    ");

    foreach ($_SESSION['cart'] as $pid => $qty) {
        $price = (float)$products[$pid]['cost'];
        $insertItem->execute([$order_id, $pid, $qty, $price]);
        $updateStock->execute([$qty, $pid]);
    }

    // Commit and clear cart 
    $pdo->commit();
    $_SESSION['cart'] = [];

    // Redirect to success page
    header('Location: ' . base_url('/order_success.php?order_id=' . $order_id));
    exit;

} catch (Exception $e) {
    // Show error
    $pdo->rollBack();
    $error = $e->getMessage();
}

// --- Page layout ---
include __DIR__ . '/includes/header.php';
?>
<section class="container">
  <h2>Checkout</h2>

  <?php if (isset($error)): ?>
    <p class="error-text">Checkout failed: <?= h($error) ?></p>
    <p>
      <a class="btn-blue" href="<?= base_url('/cart.php') ?>">Back to Cart</a>
    </p>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
