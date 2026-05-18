<?php
session_start();

// TiDB Cloud Connection Details
$host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com:4000';  // Port iko hapa!
$user = '2Sta87CGJ1DSRhL.root';
$pass = 'f0C3i3o33oNhQ1zJ';
$dbname = 'kimaro_electronics';

// Connect - port iko ndani ya host, hakuna parameter ya 5th
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully!";

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
    if (!$result) {
        echo "Query error: " . mysqli_error($conn);
        return [];
    }
    $brands = [];
    while($row = mysqli_fetch_assoc($result)) {
        $brands[] = $row;
    }
    return $brands;
}
?>