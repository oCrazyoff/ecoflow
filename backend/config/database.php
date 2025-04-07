<?php
require_once("loadEnv.php");
loadEnv(__DIR__ . '/../../senhas.env');

if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'ecoflow';
} else {
    $host = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];
    $dbname = $_ENV['DB_NAME'];
}

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
