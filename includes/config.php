<?php
session_start();

// TiDB Cloud Connection Details
$host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com';  // Badilisha na host yako halisi
$port = 4000;  // TiDB Cloud inatumia port 4000, SI 3306
$user = '2Sta87CGJ1DSRhL.root';  // Badilisha na username yako
$pass = 'f0C3i3o33oNhQ1zJ';       // Badilisha na password yako
$dbname = 'kimaro_electronics';

// Connect using port
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

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