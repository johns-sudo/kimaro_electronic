<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $sql = "UPDATE orders SET status = '$status' WHERE id = $order_id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Order status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating order status.";
    }
    redirect('orders.php');
}

// Handle order deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM orders WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Order deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting order.";
    }
    redirect('orders.php');
}

// Get all orders with product info
$orders = mysqli_query($conn, "
    SELECT o.*, p.name as product_name, p.price as product_price 
    FROM orders o 
    LEFT JOIN products p ON o.product_id = p.id 
    ORDER BY o.created_at DESC
");

// Get statistics
$stats = [];
$result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM orders GROUP BY status");
while($row = mysqli_fetch_assoc($result)) {
    $stats[$row['status']] = $row['count'];
}

// Get admin name - handle both possible session variables
$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : (isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Kimaro Electronics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
        }
        
        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header i {
            font-size: 48px;
            margin-bottom: 10px;
        }
        
        .sidebar-header h3 {
            font-size: 20px;
            margin-top: 10px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 15px;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.1);
            padding-left: 30px;
        }
        
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.15);
            border-left: 4px solid #ffc107;
        }
        
        .sidebar-menu i {
            width: 25px;
            margin-right: 12px;
            font-size: 18px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-bar {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .top-bar h2 {
            color: #2c3e50;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .top-bar h2 i {
            color: #1e3c72;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info span {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #27ae60;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #e74c3c;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card i {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-card .stat-label {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        
        /* Orders Table */
        .orders-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .orders-header {
            padding: 20px;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .orders-header h3 {
            color: #2c3e50;
        }
        
        .orders-table {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-processing {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-select {
            padding: 5px 10px;
            border: 1px solid #e1e5e9;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .btn-delete-order {
            background: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }
        
        .btn-delete-order:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }
        
        .btn-info {
            background: #3498db;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 5px;
            transition: all 0.2s;
        }
        
        .btn-info:hover {
            background: #2980b9;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            th, td {
                padding: 10px;
                font-size: 12px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-laptop-code"></i>
            <h3>Kimaro Electronics</h3>
            <p>Admin Panel</p>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="products.php">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="orders.php" class="active">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
            <h2>
                <i class="fas fa-shopping-cart"></i> 
                Orders Management
            </h2>
            <div class="user-info">
                <span>
                    <i class="fas fa-user-circle"></i> 
                    <?php echo $admin_name; ?>
                </span>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> 
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> 
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-clock" style="color: #f39c12;"></i>
                <div class="stat-number"><?php echo isset($stats['pending']) ? $stats['pending'] : 0; ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-spinner" style="color: #3498db;"></i>
                <div class="stat-number"><?php echo isset($stats['processing']) ? $stats['processing'] : 0; ?></div>
                <div class="stat-label">Processing</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle" style="color: #27ae60;"></i>
                <div class="stat-number"><?php echo isset($stats['completed']) ? $stats['completed'] : 0; ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-times-circle" style="color: #e74c3c;"></i>
                <div class="stat-number"><?php echo isset($stats['cancelled']) ? $stats['cancelled'] : 0; ?></div>
                <div class="stat-label">Cancelled</div>
            </div>
        </div>
        
        <div class="orders-container">
            <div class="orders-header">
                <h3><i class="fas fa-list"></i> All Orders</h3>
            </div>
            <div class="orders-table">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($orders) > 0): ?>
                            <?php while($order = mysqli_fetch_assoc($orders)): 
                                $total = ($order['product_price'] ? $order['product_price'] : 0) * $order['quantity'];
                            ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($order['customer_email']); ?></small><br>
                                    <?php if($order['customer_phone']): ?>
                                        <small><?php echo $order['customer_phone']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $order['product_name'] ? htmlspecialchars($order['product_name']) : 'Product Removed'; ?>
                                </td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>
                                    <?php if($order['product_price']): ?>
                                        TZS <?php echo number_format($total); ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" action="" style="display: inline-block;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="status-select" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <button onclick="deleteOrder(<?php echo $order['id']; ?>)" class="btn-delete-order">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    <?php if($order['instructions']): ?>
                                        <button onclick="showInstructions('<?php echo addslashes($order['instructions']); ?>')" class="btn-info">
                                            <i class="fas fa-info-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 40px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #bdc3c7;"></i>
                                    <p style="margin-top: 10px; color: #7f8c8d;">No orders found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script>
        function deleteOrder(id) {
            if(confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
                window.location.href = `orders.php?delete=${id}`;
            }
        }
        
        function showInstructions(instructions) {
            alert('📝 Special Instructions:\n\n' + instructions);
        }
    </script>
</body>
</html>