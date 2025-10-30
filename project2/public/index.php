<?php
//  Homepage

// Load required components
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/includes/helpers.php';

// Read filters from query
$keyword = trim($_GET['q'] ?? '');
$cat     = trim($_GET['cat'] ?? '');

// Build dynamic product query
$sql = "
    SELECT p.*, c.category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.category_id 
    WHERE 1=1
";
$params = [];

if ($keyword !== '') {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $kw = '%' . $keyword . '%';
    $params[] = $kw;
    $params[] = $kw;
}

if ($cat !== '') {
    $sql .= " AND c.category_name = ?";
    $params[] = $cat;
}

$sql .= " ORDER BY p.created_at DESC";

// Execute query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Load categories for filter dropdown
$cats = $pdo->query("SELECT category_name FROM categories ORDER BY category_name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VIT TechHUB - Online Electronics Shop</title>
  <link rel="stylesheet" href="styles.css">
</head>

<body>

<!-- Navigation bar -->
<header>
  <nav class="navbar">
    <div class="nav-container">

      <!-- Logo -->
      <div class="logo">
        <h1><a href="<?= base_url('/index.php') ?>">2025 VIT TechHUB</a></h1>
      </div>

      <!-- Navigation menu -->
      <ul class="nav-menu">
        <li><a href="#home" class="nav-link">Home</a></li>
        <li><a href="#products" class="nav-link">Shop</a></li>
        <li><a href="<?= base_url('/cart.php') ?>" class="nav-link">Cart</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="<?= base_url('/admin/dashboard.php') ?>" class="nav-link">Admin</a></li>
          <?php endif; ?>
          <li><a href="<?= base_url('/auth/logout.php') ?>" class="nav-link">Logout</a></li>
        <?php else: ?>
          <li><a href="<?= base_url('/auth/login.php') ?>" class="nav-link">Login</a></li>
          <li><a href="<?= base_url('/auth/register.php') ?>" class="nav-link">Register</a></li>
        <?php endif; ?>
      </ul>

    </div>
  </nav>
</header>

<main>
  <!-- Hero section -->
  <section id="home" class="hero">
    <div class="hero-content">
      <h2>Welcome to 2025 VIT TechHUB</h2>
      <p>Your one-stop destination for the latest technology products</p>
      <button class="cta-button" onclick="scrollToProducts()">Shop Now</button>
    </div>
  </section>

  <!-- Product listing section -->
  <section id="products" class="products">
    <div class="container">
      <h2>Shop</h2>

      <!-- Filter bar -->
      <form method="get" class="form-inline stack-wrap">
        <input 
          name="q" 
          placeholder="Search products..." 
          value="<?= h($keyword) ?>" 
          class="input-text"
        >
        <select name="cat" class="input-text">
          <option value="">All Categories</option>
          <?php foreach ($cats as $c): 
              $sel = ($cat === $c['category_name']) ? 'selected' : ''; ?>
            <option <?= $sel ?>><?= h($c['category_name']) ?></option>
          <?php endforeach; ?>
        </select>
        <button class="filter-btn" type="submit">Search</button>
        <a class="filter-btn" href="<?= base_url('/index.php#products') ?>">Reset</a>
      </form>

      <!-- Product grid -->
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
          <article class="product-card">
            <div class="product-image">
              <img src="<?= h($p['image']) ?>" alt="<?= h($p['name']) ?>">
            </div>
            <div class="product-info">
              <h3><?= h($p['name']) ?></h3>
              <p><?= h($p['description']) ?></p>
              <div class="price">$<?= h(number_format($p['cost'], 2)) ?></div>

              <!-- Add to cart form -->
              <form method="post" action="<?= base_url('/cart.php') ?>" class="add-cart-form">
                <input type="hidden" name="add_product_id" value="<?= h($p['product_id']) ?>">
                <input 
                  type="number" 
                  name="qty" 
                  min="1" 
                  max="<?= h($p['quantity']) ?>" 
                  value="1" 
                  class="input-text input-number"
                >
                <button class="add-to-cart" type="submit">Add to Cart</button>
              </form>

              <small>In stock: <?= h($p['quantity']) ?></small>
            </div>
          </article>
        <?php endforeach; ?>

        <?php if (!$products): ?>
          <div class="empty">
            <p><strong>No products found</strong></p>
            <p>Try changing your filters.</p>
            <a class="btn-blue" href="<?= base_url('/index.php#products') ?>">Go Back</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- About section -->
  <section id="about" class="about">
    <div class="container">
      <h2>About 2025 VIT TechHUB</h2>
      <div class="about-content">
        <div class="about-text">
          <p>VIT TechHUB is your one-stop destination for the latest technology products. We offer a carefully curated selection of electronics, gadgets, and accessories from top brands worldwide.</p>
          <p>Our mission is to make cutting-edge technology accessible to everyone, with competitive prices and exceptional customer service.</p>
          <ul class="features">
            <li>✓ Free shipping on orders over $100</li>
            <li>✓ 30-day return policy</li>
            <li>✓ 24/7 customer support</li>
            <li>✓ Warranty on all products</li>
          </ul>
        </div>
        <div class="about-image">
          <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?w=500&h=400&fit=crop" alt="TechStore">
        </div>
      </div>
    </div>
  </section>

  <!-- Contact section -->
  <section id="contact" class="contact">
    <div class="container">
      <h2>Contact Us</h2>
      <div class="contact-content">
        <div class="contact-info">
          <h3>Contact Information</h3>
          <div class="contact-item"><strong>Email:</strong> info@vithub.com.au</div>
          <div class="contact-item"><strong>Phone:</strong> 1300 171 755</div>
          <div class="contact-item"><strong>Address:</strong> 157/161 Gloucester St, The Rocks NSW 2000</div>
        </div>

        <!-- Contact form -->
        <form class="contact-form" id="contactForm">
          <h3>Send us a Message</h3>
          <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" required>
            <span class="error-message" id="nameError"></span>
          </div>
          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required>
            <span class="error-message" id="emailError"></span>
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject">
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            <span class="error-message" id="messageError"></span>
          </div>
          <button type="submit" class="submit-btn">Send Message</button>
        </form>
      </div>
    </div>
  </section>
</main>

<!-- Footer -->
<footer>
  <div class="container">
    <div class="footer-content">
      <div class="footer-section">
        <h3>2025 VIT TechHUB</h3>
        <p>Your ultimate destination for technology products.</p>
      </div>
      <div class="footer-section">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#home">Home</a></li>
          <li><a href="#products">Products</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Customer Service</h4>
        <ul>
          <li><a href="#">Shipping Info</a></li>
          <li><a href="#">Returns</a></li>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Support</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h4>Follow Us</h4>
        <div class="social-links">
          <a href="https://facebook.com" class="social-link" target="_blank">Facebook</a>
          <a href="https://twitter.com" class="social-link" target="_blank">Twitter</a>
          <a href="https://instagram.com" class="social-link" target="_blank">Instagram</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 VIT TechHUB. All rights reserved.</p>
    </div>
  </div>
</footer>

<!-- Global scripts -->
<script src="script.js"></script>
</body>
</html>
