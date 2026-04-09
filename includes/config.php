<?php
session_start();

// Database connection - For Local Development
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'kimaro_electronics';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
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

/**
 * Check if admin is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirect to specified URL
 * @param string $url
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Sanitize input data
 * @param mixed $data
 * @return string
 */
function escape($data) {
    global $conn;
    if ($data === null) {
        return '';
    }
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

/**
 * Get all brands from database
 * @return array
 */
function getBrands() {
    global $conn;
    // First check if brands table exists and has data
    $result = mysqli_query($conn, "SELECT * FROM brands ORDER BY name");
    if ($result && mysqli_num_rows($result) > 0) {
        $brands = [];
        while($row = mysqli_fetch_assoc($result)) {
            $brands[] = $row;
        }
        return $brands;
    } else {
        // Return default brands if table is empty or doesn't exist
        return [
            ['name' => 'HP'], ['name' => 'Dell'], ['name' => 'Lenovo'], 
            ['name' => 'Apple'], ['name' => 'ASUS'], ['name' => 'Acer'], 
            ['name' => 'MSI'], ['name' => 'Samsung'], ['name' => 'Logitech'], 
            ['name' => 'Razer'], ['name' => 'Corsair'], ['name' => 'Microsoft']
        ];
    }
}

/**
 * Get total products count
 * @return int
 */
function getTotalProducts() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

/**
 * Get total orders count
 * @return int
 */
function getTotalOrders() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

/**
 * Get pending orders count
 * @return int
 */
function getPendingOrders() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

/**
 * Get low stock products count (less than 5 units)
 * @return int
 */
function getLowStockCount() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock < 5");
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

/**
 * Get product by ID
 * @param int $id
 * @return array|null
 */
function getProductById($id) {
    global $conn;
    $id = intval($id);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Get product specifications
 * @param int $product_id
 * @return array
 */
function getProductSpecs($product_id) {
    global $conn;
    $product_id = intval($product_id);
    $result = mysqli_query($conn, "SELECT * FROM product_specs WHERE product_id = $product_id");
    $specs = [];
    while($row = mysqli_fetch_assoc($result)) {
        $specs[] = $row;
    }
    return $specs;
}
?>