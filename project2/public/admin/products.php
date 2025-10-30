<?php
//  Manage products

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';

// Restrict access to admin users only
require_admin();

//  Quick Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    $name     = trim($_POST['name'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $image    = trim($_POST['image'] ?? '');
    $cost     = (float)($_POST['cost'] ?? 0);
    $qty      = (int)($_POST['quantity'] ?? 0);
    $catName  = trim($_POST['category'] ?? '');
    $features = trim($_POST['special_features'] ?? '');

    if ($name !== '' && $catName !== '') {
        // Get or create category
        $stmt = $pdo->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $stmt->execute([$catName]);
        $cat = $stmt->fetch();

        $cat_id = $cat ? (int)$cat['category_id'] : 0;
        if (!$cat_id) {
            $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)")->execute([$catName]);
            $cat_id = (int)$pdo->lastInsertId();
        }

        // Create product
        $pdo->prepare("
            INSERT INTO products (category_id, name, description, image, cost, quantity, special_features)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ")->execute([$cat_id, $name, $desc, $image, $cost, $qty, $features]);
    }

    // Redirect back to list
    header("Location: " . base_url('/admin/products.php'));
    exit;
}

//  Delete Product
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    if ($pid > 0) {
        $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$pid]);
    }
    header("Location: " . base_url('/admin/products.php'));
    exit;
}

//  Filters search text, category, low-stock

$q        = trim($_GET['q']   ?? '');
$cat      = trim($_GET['cat'] ?? '');
$showLow  = (isset($_GET['filter']) && $_GET['filter'] === 'low');

//  Load categories for dropdown
$cats = $pdo->query("
    SELECT category_name 
    FROM categories 
    ORDER BY category_name
")->fetchAll();

//  Load products 
$sql = "
    SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE 1=1
";
$params = [];

if ($showLow) {
    $sql .= " AND p.quantity <= 30 ";
}

if ($q !== '') {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $kw = '%' . $q . '%';
    $params[] = $kw;
    $params[] = $kw;
}

if ($cat !== '') {
    $sql .= " AND c.category_name = ?";
    $params[] = $cat;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Include admin header layout
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="admin-wrap">
  <div class="admin-header">
    <div>
      <p class="breadcrumb">Admin / Manage Products</p>
      <h2 class="admin-title">Manage Products</h2>
    </div>
    <a class="btn btn-secondary" href="<?= base_url('/admin/product_edit.php') ?>">Add Product</a>
  </div>

  <!-- Filters -->
  <div class="toolbar">
    <form method="get" class="flex-row gap-sm">
      <input 
        type="text" 
        name="q" 
        class="search-input" 
        placeholder="Search products…" 
        value="<?= h($q) ?>"
      >
      <select name="cat" class="select-box">
        <option value="">All categories</option>
        <?php foreach ($cats as $c): 
            $sel = ($cat === $c['category_name']) ? 'selected' : ''; ?>
          <option <?= $sel ?>><?= h($c['category_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn-blue" type="submit">Search</button>
    </form>
  </div>

  <!-- Products Table -->
  <?php if (empty($rows)): ?>
    <div class="empty">
      <p><strong>No products found</strong></p>
      <p>Try changing your filters.</p>
      <a class="btn-blue" href="<?= base_url('/admin/products.php') ?>">Go Back</a>
    </div>
  <?php else: ?>
    <table class="table-admin">
      <thead>
        <tr>
          <th>Product</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Status</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $p): ?>
          <tr>
            <td><?= h($p['name']) ?></td>
            <td><?= h($p['category_name'] ?? '—') ?></td>
            <td>$<?= h(number_format($p['cost'], 2)) ?></td>
            <td><?= h($p['quantity']) ?></td>
            <td>
              <?php if ((int)$p['quantity'] <= 30): ?>
                <span class="badge badge-amber">Low</span>
              <?php else: ?>
                <span class="badge badge-green">OK</span>
              <?php endif; ?>
            </td>
            <td class="text-right">
              <a 
                class="btn-blue btn-small" 
                href="<?= base_url('/admin/product_edit.php?id=' . (int)$p['product_id']) ?>"
              >Edit</a>

              <a 
                class="btn-small btn-danger"
                href="<?= base_url('/admin/products.php?delete=' . (int)$p['product_id']) ?>"
                onclick="return confirm('Delete this product?')"
              >Delete</a>
            </td>
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
