<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $customer_email = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';
    $customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $instructions = isset($_POST['special_instructions']) ? trim($_POST['special_instructions']) : '';
    
    // Validate required fields
    if (empty($customer_name) || empty($customer_email)) {
        header('Location: ../product_detail.php?id=' . $product_id . '&error=missing_fields');
        exit();
    }
    
    // Clean phone number (remove non-numeric characters except + and -)
    $customer_phone = preg_replace('/[^0-9+\-]/', '', $customer_phone);
    
    // If phone is empty, set to NULL or a default value based on your DB schema
    if ($customer_phone === '') {
        $customer_phone = NULL; // or use 'N/A' if column accepts string
    }
    
    // Escape values using prepared statements (MOST SECURE)
    $sql = "INSERT INTO orders (product_id, customer_name, customer_email, customer_phone, quantity, instructions, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        // Bind parameters: i=integer, s=string, s=string, s=string, i=integer, s=string
        mysqli_stmt_bind_param($stmt, "isssis", $product_id, $customer_name, $customer_email, $customer_phone, $quantity, $instructions);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Location: ../product_detail.php?id=' . $product_id . '&success=1');
            exit();
        } else {
            mysqli_stmt_close($stmt);
            // Log error for debugging
            error_log("MySQL Error: " . mysqli_error($conn));
            header('Location: ../product_detail.php?id=' . $product_id . '&error=database');
            exit();
        }
    } else {
        header('Location: ../product_detail.php?id=' . $product_id . '&error=database');
        exit();
    }
} else {
    header('Location: ../index.php');
    exit();
}
?>