<?php
require_once 'includes/config.php';

echo "<h2>Testing Write Operation to TiDB Cloud</h2>";

// Test INSERT
$test_name = "Test Product " . date('Y-m-d H:i:s');
$sql = "INSERT INTO products (name, category, brand, price, stock, description) 
        VALUES ('$test_name', 'Test Category', 'Test Brand', 10000, 5, 'Test description')";

echo "Executing: " . $sql . "<br><br>";

if (mysqli_query($conn, $sql)) {
    $new_id = mysqli_insert_id($conn);
    echo "✅ INSERT successful!<br>";
    echo "New Product ID: " . $new_id . "<br><br>";
    
    // Verify the product was inserted
    $check = mysqli_query($conn, "SELECT * FROM products WHERE id = $new_id");
    if (mysqli_num_rows($check) > 0) {
        $product = mysqli_fetch_assoc($check);
        echo "✅ Product verified in database!<br>";
        echo "Product Name: " . $product['name'] . "<br><br>";
        
        // Clean up - delete test product
        if (mysqli_query($conn, "DELETE FROM products WHERE id = $new_id")) {
            echo "✅ Test product deleted successfully!<br>";
        } else {
            echo "❌ Delete failed: " . mysqli_error($conn) . "<br>";
        }
    }
} else {
    echo "❌ INSERT failed: " . mysqli_error($conn) . "<br>";
}

// Show products count
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
$row = mysqli_fetch_assoc($result);
echo "<br><strong>Total products in database: " . $row['total'] . "</strong>";
?>