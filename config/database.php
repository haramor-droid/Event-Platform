<?php
$pdo = new PDO(
  'mysql:host=localhost;dbname=your_db;charset=utf8mb4',
  'root',
  'Australia@2005',
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // show errors instead of failing silently
    PDO::ATTR_EMULATE_PREPARES => false,          // let MySQL do real prepared statements
  ]
);