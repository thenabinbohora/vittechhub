<?php
//  Admin header layout

// Load configuration and base path
$conf = require __DIR__ . '/config.php';
$BASE = rtrim($conf['base_path'] ?? '', '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VIT TechHUB - Admin Panel</title>

  <!-- Load global stylesheet -->
  <link rel="stylesheet" href="<?= $BASE ?>/styles.css">
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

      <!-- Admin menu -->
      <ul class="nav-menu">
        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <li><a href="<?= $BASE ?>/admin/dashboard.php" class="nav-link">Dashboard</a></li>
            <li><a href="<?= $BASE ?>/admin/products.php" class="nav-link">Manage Products</a></li>
            <li><a href="<?= $BASE ?>/admin/reports.php" class="nav-link">View Reports</a></li>
          <?php endif; ?>
          <li><a href="<?= $BASE ?>/auth/logout.php" class="nav-link">Logout</a></li>
        <?php else: ?>
          <li><a href="<?= $BASE ?>/auth/login.php" class="nav-link">Login</a></li>
          <li><a href="<?= $BASE ?>/auth/register.php" class="nav-link">Register</a></li>
        <?php endif; ?>
      </ul>

    </div>
  </nav>
</header>

<!-- Begin main admin content -->
<main style="padding-top:70px">
