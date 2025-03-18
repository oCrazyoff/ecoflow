<?php
session_start();

if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}

if (!isset($_SESSION['nome']) || !isset($_SESSION['email']) || !isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}
