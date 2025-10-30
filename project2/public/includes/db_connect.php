<?php
//  Database connection (PDO)

// Load configuration settings
$config = require __DIR__ . '/config.php';

//  Create DSN (Data Source Name) for MySQL connection
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";

try {
    //  Establish a new PDO connection
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch associative arrays by default
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
    ]);

} catch (PDOException $e) {
    //  Handle connection failure
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}
