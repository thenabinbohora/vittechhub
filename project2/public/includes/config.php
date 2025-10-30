<?php
//  Application configuration file with database credentials and base path settings

return [
  // Database connection settings
  'db_host'    => 'db',          // Docker service name or 'localhost' for local setup
  'db_name'    => 'vit_techhub', // Database name
  'db_user'    => 'root',        // Database username
  'db_pass'    => 'rootpass',    // Database password
  'db_charset' => 'utf8mb4',     // Character set for database connection

  // Application base path
  'base_path'  => '/'        
];
