<?php
//  Admin Dashboard

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';

// Restrict access to admin users only
require_admin();

// Fetch dashboard metrics from database
$totalProducts   = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders     = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalSales      = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders")->fetchColumn();
$lowStockCount   = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity <= 30")->fetchColumn();

// Include the admin header layout
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="admin-wrap">
  <div class="admin-header">
    <div>
      <p class="breadcrumb">Admin / Dashboard</p>
      <h2 class="admin-title">Admin Dashboard</h2>
    </div>
  </div>

  <!-- Stats Overview -->
  <div class="stats">
    
    <!-- Total Products -->
    <div class="card">
      <p class="card-title">Total Products</p>
      <p class="card-value"><?= h($totalProducts ?? 0) ?></p>
      <p class="card-action">
        <a class="btn-blue btn-small" href="<?= base_url('/admin/reports.php') ?>">View Report</a>
      </p>
    </div>

    <!-- Total Orders -->
    <div class="card">
      <p class="card-title">Total Orders</p>
      <p class="card-value"><?= h($totalOrders ?? 0) ?></p>
      <p class="card-action">
        <a class="btn-blue btn-small" href="<?= base_url('/admin/reports.php') ?>">View Report</a>
      </p>
    </div>

    <!-- Total Sales -->
    <div class="card">
      <p class="card-title">Total Sales</p>
      <p class="card-value">$<?= h(number_format($totalSales ?? 0, 2)) ?></p>
      <p class="card-action">
        <a class="btn-blue btn-small" href="<?= base_url('/admin/reports.php') ?>">View Report</a>
      </p>
    </div>

    <!-- Low Stock -->
    <div class="card low-stock">
      <p class="card-title">Low Stock</p>
      <p class="card-value"><?= h($lowStockCount ?? 0) ?></p>
      <p class="card-action">
        <a class="btn-blue btn-small" href="<?= base_url('/admin/products.php?filter=low') ?>">View Low Stock</a>
      </p>
    </div>

  </div> 
</section>

<?php
// Include global footer
include __DIR__ . '/../includes/footer.php';
?>
