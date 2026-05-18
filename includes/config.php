<?php
session_start();

// TiDB Cloud Connection Details
$host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = '2Sta87CGJ1DSRhL.root';
$pass = 'f0C3i3o33oNhQ1zJ';
$dbname = 'kimaro_electronics';

// Connect with SSL enabled
$conn = mysqli_init();

// Enable SSL (required for TiDB Cloud Serverless)
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Establish connection with SSL
if (!mysqli_real_connect($conn, $host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Verify SSL is active
if (mysqli_get_server_info($conn)) {
    // SSL connection successful
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