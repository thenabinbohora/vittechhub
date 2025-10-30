#!/usr/bin/env bash
set -euo pipefail

for t in 6 7 8; do
  mkdir -p "/workspaces/ITAP3012/tutorial-$t/public"
  if [ ! -f "/workspaces/ITAP3012/tutorial-$t/public/index.php" ]; then
    cat > "/workspaces/ITAP3012/tutorial-$t/public/index.php" <<PHP
<?php
echo "ITAP3012 — Tutorial $t OK — PHP: " . PHP_VERSION;
PHP
  fi
done

cat > "/workspaces/ITAP3012/tutorial-6/public/db_test.php" <<'PHP'
<?php
$dsn = 'mysql:host=db;port=3306;dbname=itap_t6;charset=utf8mb4';
try {
  $pdo = new PDO($dsn, 'root', 'rootpass', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  echo "✅ Connected to itap_t6";
} catch (Throwable $e) {
  http_response_code(500);
  echo "❌ DB error: " . htmlspecialchars($e->getMessage());
}
PHP
