<?php

  $dbHost = "localhost";
  $dbUser = "root";
  $dbPassword = "S3rv3r";
  $dbName = "ppmpee";

  // $dbHost = "localhost";
  // $dbUser = "root";
  // $dbPassword = "password";
  // $dbName = "bghmc";

  try {
    $dsn = "mysql:host=" . $dbHost . ";dbname=" . $dbName;
    $pdo = new PDO($dsn, $dbUser, $dbPassword);
    // echo "connected";
  } catch(PDOException $e) {
    echo "DB Connection Failed: " . $e->getMessage();
  }
  
  $status = "connected";
?>