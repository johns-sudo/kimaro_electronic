<?php
require_once 'includes/config.php';

// Get category filter
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build query
$sql = "SELECT * FROM products WHERE 1=1";
if (!empty($category)) {
    $sql .= " AND category = '$category'";
}
if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR brand LIKE '%$search%' OR description LIKE '%$search%')";
}
$sql .= " ORDER BY featured DESC, id DESC";

$products = mysqli_query($conn, $sql);
$total_products = mysqli_num_rows($products);

// Get all categories for filter
$categories = mysqli_query($conn, "SELECT DISTINCT category, COUNT(*) as count FROM products GROUP BY category");

// Get brands for filter
$brands = mysqli_query($conn, "SELECT DISTINCT brand, COUNT(*) as count FROM products GROUP BY brand");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bidhaa Zetu - Kimaro Computers</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: #f8f9fc;
        }
        
        /* Navigation - Same as index */
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 15px 5%;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .navbar.scrolled {
            padding: 10px 5%;
            background: white;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 32px;
        }
        
        .nav-links {
            display: flex;
            gap: 35px;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }
        
        .nav-links a:before {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            transition: width 0.3s;
        }
        
        .nav-links a:hover:before,
        .nav-links a.active:before {
            width: 100%;
        }
        
        .nav-links a.active {
            color: #1e3c72;
        }
        
        .btn-contact {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white !important;
            padding: 8px 20px;
            border-radius: 50px;
        }
        
        .btn-contact:before {
            display: none;
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 80px 5% 50px;
            color: white;
            text-align: center;
            margin-top: 0;
        }
        
        .page-header h1 {
            font-size: 48px;
            margin-bottom: 15px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .page-header p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 5%;
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .filter-header h3 {
            color: #2c3e50;
            font-size: 18px;
        }
        
        .results-count {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            border-top: 1px solid #e1e5e9;
            padding-top: 20px;
        }
        
        .filter-tab {
            padding: 8px 20px;
            border-radius: 30px;
            background: #f8f9fa;
            color: #2c3e50;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .filter-tab:hover,
        .filter-tab.active {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            transform: translateY(-2px);
        }
        
        .search-form {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .search-input {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e1e5e9;
            border-radius: 50px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #1e3c72;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }
        
        .clear-filter {
            color: #e74c3c;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #1e3c72;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }
        
        .product-image {
            height: 250px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-name {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .brand-badge {
            display: inline-block;
            background: #e8eaf6;
            color: #3f51b5;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .product-price {
            color: #27ae60;
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .product-stock {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .btn-view {
            width: 100%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
        }
        
        .empty-state i {
            font-size: 80px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        
        .empty-state .btn-shop {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
        }
        
        /* Footer - Same as index */
        .footer {
            background: #0a1a2e;
            color: white;
            padding: 60px 5% 30px;
            margin-top: 60px;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-section h3 {
            font-size: 20px;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .footer-section h3:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 50px;
            height: 2px;
            background: #ffc107;
        }
        
        .footer-section p {
            line-height: 1.8;
            color: rgba(255,255,255,0.7);
        }
        
        .footer-section a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: #ffc107;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: #ffc107;
            color: #1e3c72;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.6);
            font-size: 14px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 32px;
            }
            
            .nav-links {
                display: none;
            }
            
            .filter-header {
                flex-direction: column;
                text-align: center;
            }
            
            .filter-tabs {
                justify-content: center;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-microchip"></i>
                <span>Kimaro Computers</span>
            </a>
            <div class="nav-links">
                <a href="index.php">Nyumbani</a>
                <a href="products.php" class="active">Bidhaa</a>
                <a href="about.php">Kuhusu Sisi</a>
                <a href="contact.php" class="btn-contact">Wasiliana</a>
            </div>
        </div>
    </nav>
    
    <div class="page-header">
        <h1>🛍️ Bidhaa Zetu</h1>
        <p>Chagua kutoka kwenye mkusanyiko wetu wa vifaa vya kompyuta vya ubora wa juu</p>
    </div>
    
    <div class="container">
        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <h3><i class="fas fa-filter"></i> Chagua Kategoria</h3>
                <div class="results-count">
                    <i class="fas fa-database"></i> Bidhaa <?php echo $total_products; ?>
                </div>
            </div>
            
            <div class="filter-tabs">
                <a href="products.php" class="filter-tab <?php echo empty($category) && empty($search) ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i> Zote
                </a>
                <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                <a href="products.php?category=<?php echo urlencode($cat['category']); ?>" 
                   class="filter-tab <?php echo $category == $cat['category'] ? 'active' : ''; ?>">
                    <?php 
                    $icons = [
                        'Laptop' => '💻',
                        'Desktop' => '🖥️',
                        'Monitor' => '📺',
                        'Accessories' => '🔌',
                        'Mouse' => '🐭',
                        'Keyboard' => '⌨️',
                        'Gaming' => '🎮'
                    ];
                    echo isset($icons[$cat['category']]) ? $icons[$cat['category']] : '📦';
                    ?> 
                    <?php echo $cat['category']; ?> 
                    <span style="font-size: 11px;">(<?php echo $cat['count']; ?>)</span>
                </a>
                <?php endwhile; ?>
            </div>
            
            <form class="search-form" method="GET" action="">
                <input type="text" name="search" class="search-input" placeholder="Tafuta bidhaa kwa jina, brand au maelezo..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Tafuta
                </button>
                <?php if(!empty($search) || !empty($category)): ?>
                    <a href="products.php" class="clear-filter">
                        <i class="fas fa-times"></i> Futa
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Products Grid -->
        <?php if(mysqli_num_rows($products) > 0): ?>
        <div class="products-grid">
            <?php while($product = mysqli_fetch_assoc($products)): ?>
            <div class="product-card" onclick="viewProduct(<?php echo $product['id']; ?>)">
                <?php if($product['featured']): ?>
                    <div class="product-badge">
                        <i class="fas fa-star"></i> Maarufu
                    </div>
                <?php endif; ?>
                <div class="product-image">
                    <?php if($product['image'] && file_exists("uploads/".$product['image'])): ?>
                        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div style="font-size: 64px; color: #bdc3c7;">
                            <i class="fas fa-microchip"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="brand-badge">
                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product['brand']); ?>
                    </div>
                    <div class="product-price">
                        TZS <?php echo number_format($product['price']); ?>
                    </div>
                    <div class="product-stock">
                        <i class="fas fa-boxes"></i> Imesalia: <?php echo $product['stock']; ?>
                    </div>
                    <button class="btn-view" onclick="event.stopPropagation(); viewProduct(<?php echo $product['id']; ?>)">
                        Tazama Maelezo <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>Hakuna Bidhaa Zilizopatikana</h3>
            <p>Jaribu kutumia vigezo vingine vya kutafuta au angalia bidhaa zetu zote</p>
            <a href="products.php" class="btn-shop">Ona Bidhaa Zote</a>
        </div>
        <?php endif; ?>
    </div>
    
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Kimaro Computers</h3>
                <p>Mtoa huduma mkuu wa vifaa vya kompyuta Tanzania. Tunatoa bidhaa bora na huduma ya kiwango cha juu kwa wateja wetu.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Viungo Muhimu</h3>
                <a href="index.php">Nyumbani</a>
                <a href="products.php">Bidhaa Zote</a>
                <a href="about.php">Kuhusu Sisi</a>
                <a href="contact.php">Wasiliana Nasi</a>
            </div>
            <div class="footer-section">
                <h3>Mawasiliano</h3>
                <p><i class="fas fa-phone"></i> +255 782 751 622</p>
                <p><i class="fas fa-envelope"></i> kimarojohn92@gmail.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Dar es Salaam, Tanzania</p>
            </div>
            <div class="footer-section">
                <h3>Saa za Kufungua</h3>
                <p>Jumatatu - Ijumaa: 8:00 - 18:00</p>
                <p>Jumamosi: 9:00 - 15:00</p>
                <p>Jumapili: Imefungwa</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Kimaro Computers. Haki zote zimehifadhiwa. | Imetengenezwa na J col</p>
        </div>
    </footer>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        function viewProduct(id) {
            window.location.href = 'product_detail.php?id=' + id;
        }
    </script>
</body>
</html>