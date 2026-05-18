<?php
session_start();

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'kimaro_electronics';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Simple functions
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function escape($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Get all brands
function getBrands() {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM brands ORDER BY name");
    $brands = [];
    while($row = mysqli_fetch_assoc($result)) {
        $brands[] = $row;
    }
    return $brands;
}
?>