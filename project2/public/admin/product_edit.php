<?php
//  Admin Product Editor

// Load required components
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/helpers.php';
require_admin(); // restrict to admin users only

// Load existing product for editing
$product = null;
if (isset($_GET['id'])) {
    $pid = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch();
}

// Load all categories for dropdown
$cats = $pdo->query("
    SELECT category_id, category_name 
    FROM categories 
    ORDER BY category_name
")->fetchAll();

// Handle form submission (create or update product)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form input safely
    $name      = trim($_POST['name'] ?? '');
    $desc      = trim($_POST['description'] ?? '');
    $image     = trim($_POST['image'] ?? '');
    $cost      = (float)($_POST['cost'] ?? 0);
    $qty       = (int)($_POST['quantity'] ?? 0);
    $catId     = (int)($_POST['category_id'] ?? 0);
    $features  = trim($_POST['special_features'] ?? '');

    // Only process valid inputs
    if ($name && $catId) {
        if ($product) {
            // Update existing product
            $sql = "
                UPDATE products 
                SET category_id=?, name=?, description=?, image=?, cost=?, quantity=?, special_features=? 
                WHERE product_id=?
            ";
            $pdo->prepare($sql)->execute([
                $catId, $name, $desc, $image, $cost, $qty, $features, $product['product_id']
            ]);
        } else {
            // Create new product 
            $sql = "
                INSERT INTO products (category_id, name, description, image, cost, quantity, special_features) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
            $pdo->prepare($sql)->execute([
                $catId, $name, $desc, $image, $cost, $qty, $features
            ]);
        }
    }

    // Redirect to product management page
    header("Location: " . base_url('/admin/products.php'));
    exit;
}

// Include admin header template
include __DIR__ . '/../includes/admin_header.php';
?>

<section class="admin-wrap">
  <div class="admin-header">
    <div>
      <p class="breadcrumb">Admin / <?= $product ? 'Edit Product' : 'Add Product' ?></p>
      <h2 class="admin-title"><?= $product ? 'Edit Product' : 'Add Product' ?></h2>
    </div>
  </div>

  <!-- Product Form -->
  <form method="post" class="card form-grid">

    <!-- Product Name -->
    <div class="form-item">
      <label>Product Name</label>
      <input class="input-box" name="name" value="<?= h($product['name'] ?? '') ?>" required>
    </div>

    <!-- Category Dropdown -->
    <div class="form-item">
      <label>Category</label>
      <select class="select-box" name="category_id" required>
        <option value="">Select category</option>
        <?php foreach ($cats as $c): ?>
          <option 
            value="<?= (int)$c['category_id'] ?>"
            <?= isset($product['category_id']) && $product['category_id'] == $c['category_id'] ? 'selected' : '' ?>>
            <?= h($c['category_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Price -->
    <div class="form-item">
      <label>Price</label>
      <input class="input-box" type="number" step="0.01" name="cost"
             value="<?= h($product['cost'] ?? '') ?>" required>
    </div>

    <!-- Stock Quantity -->
    <div class="form-item">
      <label>Stock Quantity</label>
      <input class="input-box" type="number" name="quantity"
             value="<?= h($product['quantity'] ?? 0) ?>" min="0" required>
    </div>

    <!-- Description -->
    <div class="form-item full-width">
      <label>Description</label>
      <textarea class="input-box" name="description" rows="4"><?= h($product['description'] ?? '') ?></textarea>
    </div>

    <!-- Special Features -->
    <div class="form-item full-width">
      <label>Special Features</label>
      <textarea class="input-box" name="special_features" rows="3"><?= h($product['special_features'] ?? '') ?></textarea>
    </div>

    <!-- Image URL -->
    <div class="form-item full-width">
      <label>Image URL</label>
      <input class="input-box" name="image" value="<?= h($product['image'] ?? '') ?>">
    </div>

    <!-- Form Buttons -->
    <div class="toolbar" style="justify-content: flex-end;">
      <a class="btn-danger btn-small" href="<?= base_url('/admin/products.php') ?>">Cancel</a>
      <button class="btn-main" type="submit">
        <?= $product ? 'Save Changes' : 'Create Product' ?>
      </button>
    </div>

  </form>
</section>
<?php
// Include global footer
include __DIR__ . '/../includes/footer.php';
?>
