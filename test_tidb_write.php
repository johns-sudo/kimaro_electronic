<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// TiDB Cloud Connection
$host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = '2Sta87CGJ1DSRhL.root';
$password = 'f0C3i3o33oNhQ1zJ';
$database = 'kimaro_electronics';

echo "<h2>🔍 Testing TiDB Cloud Write Operations</h2>";
echo "Host: $host<br>";
echo "Port: $port<br>";
echo "User: $user<br>";
echo "Database: $database<br><br>";

// Connect with SSL
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connection failed: " . mysqli_connect_error());
}

echo "✅ Connected successfully!<br><br>";

// Test 1: Insert a product
echo "<h3>📝 Test 1: INSERT Product</h3>";
$test_name = "Test Product " . date('Y-m-d H:i:s');
$sql = "INSERT INTO products (name, category, brand, price, stock, description) 
        VALUES ('$test_name', 'Test Category', 'Test Brand', 50000, 10, 'This is a test product')";

if (mysqli_query($conn, $sql)) {
    $new_id = mysqli_insert_id($conn);
    echo "✅ INSERT successful!<br>";
    echo "   New Product ID: " . $new_id . "<br>";
    
    // Test 2: Select the inserted product
    echo "<h3>📖 Test 2: SELECT Product</h3>";
    $select = mysqli_query($conn, "SELECT * FROM products WHERE id = $new_id");
    if ($product = mysqli_fetch_assoc($select)) {
        echo "✅ SELECT successful!<br>";
        echo "   Product Name: " . $product['name'] . "<br>";
        
        // Test 3: Update the product
        echo "<h3>✏️ Test 3: UPDATE Product</h3>";
        $update = "UPDATE products SET price = 75000, stock = 25 WHERE id = $new_id";
        if (mysqli_query($conn, $update)) {
            echo "✅ UPDATE successful!<br>";
        } else {
            echo "❌ UPDATE failed: " . mysqli_error($conn) . "<br>";
        }
        
        // Test 4: Delete the product
        echo "<h3>🗑️ Test 4: DELETE Product</h3>";
        $delete = "DELETE FROM products WHERE id = $new_id";
        if (mysqli_query($conn, $delete)) {
            echo "✅ DELETE successful!<br>";
        } else {
            echo "❌ DELETE failed: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "❌ SELECT failed: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "❌ INSERT failed: " . mysqli_error($conn) . "<br>";
}

// Show current products count
$count_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$count = mysqli_fetch_assoc($count_result);
echo "<hr>";
echo "<h3>📊 Current Products in Database: " . $count['total'] . "</h3>";

mysqli_close($conn);
echo "<br><h3>✅ Test Complete!</h3>";
?>