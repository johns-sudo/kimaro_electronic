<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    die("Please login as admin first");
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");

echo "<h2>Products in Database</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Created</th></tr>";

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['category']}</td>";
    echo "<td>{$row['price']}</td>";
    echo "<td>{$row['stock']}</td>";
    echo "<td>{$row['created_at']}</td>";
    echo "</tr>";
}

echo "</table>";
?>