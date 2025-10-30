<?php
//  Order Success Page

// Load required components
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/helpers.php';

// Restrict access to logged-in users only
require_login();

// Get order ID from query
$order_id = (int)($_GET['order_id'] ?? 0);

// Include header layout
include __DIR__ . '/includes/header.php';
?>
<section class="container">
  <h2>Order Successful</h2>

  <p>
    Thank you! Your order 
    <strong>#<?= h($order_id) ?></strong> 
    has been successfully placed.
  </p>

  <p>
    <a class="btn-blue" href="<?= base_url('/index.php#products') ?>">
      Continue Shopping
    </a>
  </p>
</section>

<?php
// Include global footer
include __DIR__ . '/includes/footer.php';
?>
