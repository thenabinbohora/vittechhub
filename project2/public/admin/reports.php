<?php
//  Sales reports

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';

// Restrict access to admin users only
require_admin();

//  Filters 
$from = trim($_GET['from'] ?? '');
$to   = trim($_GET['to']   ?? '');
$cat  = trim($_GET['cat']  ?? '');

$where  = " WHERE 1=1 ";
$params = [];

if ($from !== '') {
    $where   .= " AND o.order_date >= ? ";
    $params[] = $from . " 00:00:00";
}

if ($to !== '') {
    $where   .= " AND o.order_date <= ? ";
    $params[] = $to . " 23:59:59";
}

if ($cat !== '') {
    $where   .= " AND c.category_name = ? ";
    $params[] = $cat;
}

//  Query for total revenue and quantity per product
$sql = "
    SELECT 
      c.category_name,
      p.name AS product_name,
      SUM(oi.quantity)            AS total_quantity,
      SUM(oi.quantity * oi.price) AS total_revenue
    FROM order_items oi
    JOIN orders    o ON oi.order_id  = o.order_id
    JOIN products  p ON oi.product_id = p.product_id
    JOIN categories c ON p.category_id = c.category_id
    $where
    GROUP BY c.category_name, p.name
    ORDER BY total_revenue DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

//  Load categories for dropdown
$cats = $pdo->query("
    SELECT category_name 
    FROM categories 
    ORDER BY category_name
")->fetchAll();

// Include admin header layout
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="admin-wrap">
  <div class="admin-header">
    <div>
      <p class="breadcrumb">Admin / Reports</p>
      <h2 class="admin-title">Sales Reports</h2>
    </div>
  </div>

  <!-- Filter toolbar -->
  <form method="get" class="toolbar card">
    <input type="date" name="from" class="input-box" value="<?= h($from) ?>">
    <input type="date" name="to"   class="input-box" value="<?= h($to)   ?>">

    <select name="cat" class="select-box">
      <option value="">All categories</option>
      <?php foreach ($cats as $c): 
        $sel = ($cat === $c['category_name']) ? 'selected' : ''; ?>
        <option <?= $sel ?>><?= h($c['category_name']) ?></option>
      <?php endforeach; ?>
    </select>

    <button class="btn-blue" type="submit">Apply</button>
  </form>

  <!-- Results table -->
  <?php if (empty($rows)): ?>
    <div class="empty">No results for the selected filters.</div>
  <?php else: ?>
    <table class="table-admin">
      <thead>
        <tr>
          <th>Category</th>
          <th>Product</th>
          <th>Quantity Sold</th>
          <th>Total Revenue ($)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= h($r['category_name']) ?></td>
            <td><?= h($r['product_name']) ?></td>
            <td><?= h($r['total_quantity']) ?></td>
            <td>$<?= h(number_format($r['total_revenue'], 2)) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php
// --- Include global footer ---
include __DIR__ . '/../includes/footer.php';
?>
