<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $name = sanitize($_POST['name']);
    $category = sanitize($_POST['category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = sanitize($_POST['description']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../assets/images/products/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = 'assets/images/products/' . $new_filename;
            }
        }
    }
    
    if ($product_id > 0) {
        // Update existing product
        if ($image_url) {
            $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, image_url=?, stock=?, featured=? WHERE id=?");
            $stmt->bind_param("ssdssiii", $name, $category, $price, $description, $image_url, $stock, $featured, $product_id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, description=?, stock=?, featured=? WHERE id=?");
            $stmt->bind_param("ssdsiii", $name, $category, $price, $description, $stock, $featured, $product_id);
        }
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product updated successfully';
        } else {
            $response['message'] = 'Error updating product';
        }
        $stmt->close();
    } else {
        // Add new product
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, description, image_url, stock, featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssii", $name, $category, $price, $description, $image_url, $stock, $featured);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Product added successfully';
        } else {
            $response['message'] = 'Error adding product';
        }
        $stmt->close();
    }
}

echo json_encode($response);
?>