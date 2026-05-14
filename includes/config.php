<?php
session_start();

// TiDB Cloud Connection
$db_host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com';
$db_port = 4000;
$db_user = '2Sta87CGJ1DSRhL.root';  // Username sahihi
$db_password = 'f0C3i3o33oNhQ1zJ';
$db_name = 'kimaro_electronics';

// Create connection with SSL
$conn = mysqli_init();
if (!$conn) {
    die("Connection initialization failed");
}

// Enable SSL (required for TiDB Cloud)
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Connect
if (!mysqli_real_connect($conn, $db_host, $db_user, $db_password, $db_name, $db_port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Site configuration
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host_url = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $host_url;

define('SITE_NAME', 'Kimaro Computers');
define('SITE_URL', $base_url);

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function escape($data) {
    global $conn;
    if ($data === null) return '';
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

function getBrands() {
    return [
        ['name' => 'HP'], ['name' => 'Dell'], ['name' => 'Lenovo'], 
        ['name' => 'Apple'], ['name' => 'ASUS'], ['name' => 'Acer'], 
        ['name' => 'MSI'], ['name' => 'Samsung'], ['name' => 'Logitech'], 
        ['name' => 'Razer'], ['name' => 'Corsair'], ['name' => 'Microsoft']
    ];
}

// Other functions...
?>