<?php
session_start();


/**
 * Database Configuration for Smart Cashew - TiDB Cloud (Render Ready)
 */

// Get database settings from environment variables (set on Render)
$db_host = getenv('DB_HOST') ?: 'gateway01.eu-central-1.prod.aws.tidbcloud.com:4000';
$db_user = getenv('DB_USER') ?: '2Sta87CGJ1DSRhL.root';
$db_password = getenv('DB_PASSWORD') ?: 'f0C3i3o33oNhQ1zJ';
$db_name = getenv('DB_NAME') ?: 'kimaro_electronics';

// Database settings
define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_NAME', $db_name);

// Charset
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_general_ci');

// SSL is required for TiDB Cloud
define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);

// Create connection using MySQLi
$conn = mysqli_init();

if (!$conn) {
    die("MySQLi initialization failed");
}

// Set SSL flags
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Connect to database
if (!mysqli_real_connect($conn, DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 4000, NULL, MYSQLI_CLIENT_SSL)) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, DB_CHARSET);

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