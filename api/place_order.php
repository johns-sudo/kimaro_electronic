<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $customer_name = isset($_POST['customer_name']) ? mysqli_real_escape_string($conn, trim($_POST['customer_name'])) : '';
    $customer_email = isset($_POST['customer_email']) ? mysqli_real_escape_string($conn, trim($_POST['customer_email'])) : '';
    $customer_phone = isset($_POST['customer_phone']) ? mysqli_real_escape_string($conn, trim($_POST['customer_phone'])) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $instructions = isset($_POST['special_instructions']) ? mysqli_real_escape_string($conn, trim($_POST['special_instructions'])) : '';
    
    // Validate
    if (empty($customer_name) || empty($customer_email)) {
        header('Location: ../product_detail.php?id=' . $product_id . '&error=missing_fields');
        exit();
    }
    
    // Insert order
    $sql = "INSERT INTO orders (product_id, customer_name, customer_email, customer_phone, quantity, instructions, status) 
            VALUES ($product_id, '$customer_name', '$customer_email', '$customer_phone', $quantity, '$instructions', 'pending')";
    
    if (mysqli_query($conn, $sql)) {
        header('Location: ../product_detail.php?id=' . $product_id . '&success=1');
    } else {
        header('Location: ../product_detail.php?id=' . $product_id . '&error=database');
    }
    exit();
} else {
    header('Location: ../index.php');
    exit();
}
?>