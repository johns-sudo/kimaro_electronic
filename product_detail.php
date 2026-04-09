<?php
require_once 'includes/config.php';

// Get product ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    header('Location: index.php');
    exit();
}

// Get product details
$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: index.php');
    exit();
}

$product = mysqli_fetch_assoc($result);

// Get product specifications
$specs = mysqli_query($conn, "SELECT * FROM product_specs WHERE product_id = $id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Kimaro Electronics</title>
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
        
        /* Navigation */
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e3c72;
            text-decoration: none;
        }
        
        .logo i {
            margin-right: 8px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #2c3e50;
            margin-left: 25px;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #1e3c72;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: #1e3c72;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 30px;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .back-btn:hover {
            transform: translateX(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        /* Product Detail */
        .product-detail {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        
        /* Product Image */
        .product-image {
            background: #f8f9fa;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }
        
        .product-image img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 10px;
        }
        
        .product-image .no-image {
            text-align: center;
            color: #bdc3c7;
        }
        
        .product-image .no-image i {
            font-size: 120px;
            margin-bottom: 20px;
        }
        
        /* Product Info */
        .product-info {
            padding: 40px;
        }
        
        .product-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .brand-badge {
            display: inline-block;
            background: #e8eaf6;
            color: #3f51b5;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .product-price {
            font-size: 32px;
            color: #27ae60;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .product-stock {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        
        .stock-high {
            background: #d4edda;
            color: #155724;
        }
        
        .stock-medium {
            background: #fff3cd;
            color: #856404;
        }
        
        .stock-low {
            background: #f8d7da;
            color: #721c24;
        }
        
        .description {
            margin: 25px 0;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }
        
        .description h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .description p {
            color: #555;
            line-height: 1.6;
        }
        
        /* Specifications */
        .specs {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .specs h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .spec-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .spec-item:last-child {
            border-bottom: none;
        }
        
        .spec-name {
            width: 40%;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .spec-value {
            width: 60%;
            color: #555;
        }
        
        /* Request Form */
        .request-form {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
        }
        
        .request-form h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e3c72;
        }
        
        .btn-request {
            width: 100%;
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-request:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39,174,96,0.3);
        }
        
        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 30px;
            margin-top: 60px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
            }
            
            .product-name {
                font-size: 24px;
            }
            
            .product-price {
                font-size: 28px;
            }
            
            .spec-item {
                flex-direction: column;
            }
            
            .spec-name, .spec-value {
                width: 100%;
            }
            
            .spec-name {
                margin-bottom: 5px;
            }
        }
        
        /* Alert */
        .alert {
            padding: 15px;
            border-radius: 8px;
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
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-laptop-code"></i> Kimaro Electronics
            </a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="request.php">Request</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <a href="javascript:history.back()" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Products
        </a>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Request submitted successfully! We will contact you soon.
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> Error submitting request. Please try again.
            </div>
        <?php endif; ?>
        
        <div class="product-detail">
            <div class="product-image">
                <?php if($product['image'] && file_exists("uploads/".$product['image'])): ?>
                    <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="no-image">
                        <i class="fas fa-microchip"></i>
                        <p>No Image Available</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info">
                <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                <span class="brand-badge">
                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['brand']); ?>
                </span>
                
                <div class="product-price">
                    TZS <?php echo number_format($product['price']); ?>
                </div>
                
                <?php
                $stockClass = '';
                $stockText = '';
                if($product['stock'] >= 10) {
                    $stockClass = 'stock-high';
                    $stockText = 'In Stock';
                } elseif($product['stock'] >= 1) {
                    $stockClass = 'stock-medium';
                    $stockText = 'Low Stock';
                } else {
                    $stockClass = 'stock-low';
                    $stockText = 'Out of Stock';
                }
                ?>
                <div class="product-stock <?php echo $stockClass; ?>">
                    <i class="fas fa-boxes"></i> <?php echo $stockText; ?> (<?php echo $product['stock']; ?> units)
                </div>
                
                <div class="description">
                    <h3><i class="fas fa-align-left"></i> Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <?php if(mysqli_num_rows($specs) > 0): ?>
                <div class="specs">
                    <h3><i class="fas fa-microchip"></i> Technical Specifications</h3>
                    <?php while($spec = mysqli_fetch_assoc($specs)): ?>
                    <div class="spec-item">
                        <div class="spec-name"><?php echo htmlspecialchars($spec['spec_name']); ?></div>
                        <div class="spec-value"><?php echo htmlspecialchars($spec['spec_value']); ?></div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
                
                <div class="request-form">
                    <h3><i class="fas fa-shopping-cart"></i> Request This Product</h3>
                    <form action="api/place_order.php" method="POST" onsubmit="return validateForm()">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        
                        <div class="form-group">
                            <input type="text" name="customer_name" placeholder="Your Full Name *" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="customer_email" placeholder="Your Email Address *" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" name="customer_phone" placeholder="Phone Number (Optional)">
                        </div>
                        <div class="form-group">
                            <input type="number" name="quantity" placeholder="Quantity" value="1" min="1" required>
                        </div>
                        <div class="form-group">
                            <textarea name="special_instructions" rows="3" placeholder="Special Instructions (Optional)"></textarea>
                        </div>
                        <button type="submit" class="btn-request">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2026 Kimaro Electronics. All rights reserved.</p>
        <p>📍 Dar es Salaam, Tanzania | 📞 +255 782751622</p>
    </footer>
    
    <script>
        function validateForm() {
            const name = document.querySelector('input[name="customer_name"]').value;
            const email = document.querySelector('input[name="customer_email"]').value;
            const quantity = document.querySelector('input[name="quantity"]').value;
            
            if(name.trim() === '') {
                alert('Please enter your name');
                return false;
            }
            
            if(email.trim() === '') {
                alert('Please enter your email');
                return false;
            }
            
            if(!email.includes('@')) {
                alert('Please enter a valid email address');
                return false;
            }
            
            if(quantity < 1) {
                alert('Quantity must be at least 1');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>