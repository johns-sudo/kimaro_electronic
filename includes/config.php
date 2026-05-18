<?php
session_start();

// TiDB Cloud Connection Details
$host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com:4000';  // Port ndani ya host
$user = '2Sta87CGJ1DSRhL.root';
$pass = 'f0C3i3o33oNhQ1zJ';
$dbname = 'kimaro_electronics';

// Connect with SSL
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Host tayari ina port, so usiweke port parameter tena
if (!mysqli_real_connect($conn, $host, $user, $pass, $dbname, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "✅ Connected successfully!";

// Simple functions (hazibadiliki)
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