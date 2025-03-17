<?php
/*$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ecoflow';*/

$host = 'sql202.infinityfree.com';
$username = 'if0_38495560';
$password = 'hblgZUg4FR8y';
$dbname = 'if0_38495560_ecoflow';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
