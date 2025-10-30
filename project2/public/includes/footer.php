<?php
//  Global footer layout

// Load configuration and base path
$conf = require __DIR__ . '/config.php';
$BASE = rtrim($conf['base_path'] ?? '', '/');
?>
</main>

<!-- Footer -->
<footer style="margin-top:40px;">
  <div class="container">
    <div class="footer-content">

      <!-- Footer brand -->
      <div class="footer-section">
        <h3><a href="<?= $BASE ?>/index.php">2025 VIT TechHUB</a></h3>
      </div>

    </div>

    <!-- Footer bottom -->
    <div class="footer-bottom">
      <p>&copy; 2025 VIT TechHUB. All rights reserved.</p>
    </div>
  </div>
</footer>

<!-- Global script -->
<script src="<?= $BASE ?>/script.js"></script>
</body>
</html>
