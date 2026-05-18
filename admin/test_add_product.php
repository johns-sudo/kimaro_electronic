<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    die("Please login as admin first");
}

// Test adding a product directly
$name = "Test Product";
$category = "Accessories";
$price = 10000;
$description = "This is a test product";
$stock = 10;
$featured = 0;

$stmt = $conn->prepare("INSERT INTO products (name, category, price, description, stock, featured) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssdsii", $name, $category, $price, $description, $stock, $featured);

if ($stmt->execute()) {
    echo "✅ Test product added successfully! ID: " . $stmt->insert_id;
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
?>