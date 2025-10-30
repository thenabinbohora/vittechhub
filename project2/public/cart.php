<?php
//  Shopping Cart

// Load required components
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/helpers.php';

//  Ensure cart array exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

//  Add item to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product_id'])) {
    $pid = (int)$_POST['add_product_id'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + $qty;

    header('Location: ' . base_url('/cart.php'));
    exit;
}

//  Update quantities / remove items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if (isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $pid => $q) {
            $pid = (int)$pid;
            $q   = (int)$q;

            if ($q <= 0) {
                unset($_SESSION['cart'][$pid]);
            } else {
                $_SESSION['cart'][$pid] = $q;
            }
        }
    }
    header('Location: ' . base_url('/cart.php'));
    exit;
}

//  Build cart items and totals
$items = [];
$total = 0.00;

if (!empty($_SESSION['cart'])) {
    // Prepare a safe IN() query
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $ph  = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id IN ($ph)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();

    foreach ($rows as $r) {
        $pid  = (int)$r['product_id'];
        $qty  = (int)($_SESSION['cart'][$pid] ?? 0);
        $line = $qty * (float)$r['cost'];

        $total += $line;
        $items[] = [
            'p'    => $r,
            'qty'  => $qty,
            'line' => $line,
        ];
    }
}

//  Page layout
include __DIR__ . '/includes/header.php';
?>
<section class="container">
  <h2>Your Cart</h2>

  <?php if (empty($items)): ?>
    <p>Cart is empty.</p>
    <a class="btn-blue" href="<?= base_url('/index.php#products') ?>">Continue shopping</a>

  <?php else: ?>
    <form method="post">
      <table class="table">
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-center">Price</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Line</th>
          </tr>
        </thead>

        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= h($it['p']['name']) ?></td>

              <td class="text-center">
                $<?= h(number_format((float)$it['p']['cost'], 2)) ?>
              </td>

              <td class="text-center">
                <input
                  type="number"
                  name="qty[<?= h($it['p']['product_id']) ?>]"
                  value="<?= h($it['qty']) ?>"
                  min="0"
                  class="input-text input-number"
                >
              </td>

              <td class="text-center">
                $<?= h(number_format($it['line'], 2)) ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <p class="cart-total"><strong>Total:</strong> $<?= h(number_format($total, 2)) ?></p>

      <div class="actions">
        <button class="submit-btn btn-secondary" type="submit" name="update" value="1">
          Update Cart
        </button>
        <a class="submit-btn" href="<?= base_url('/checkout.php') ?>">
          Proceed to Checkout
        </a>
      </div>
    </form>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
