<?php
    require("vendor/autoload.php");
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $dotenv->required(['DATABASE_HOST', 'DATABASE_NAME', 'DATABASE_USER'])->notEmpty();

    $db_host = $_ENV['DATABASE_HOST'];
    $db_name = $_ENV['DATABASE_NAME'];
    $db_user = $_ENV['DATABASE_USER'];
    $db_pass = $_ENV['DATABASE_PASS'];

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Connect to the database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $conn->set_charset("utf8mb4");
?>