<?php
require_once 'includes/config.php';

// Get featured products
$featured = mysqli_query($conn, "SELECT * FROM products WHERE featured=1 LIMIT 8");

// Get latest products
$latest = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 4");

// Get categories count
$categories = [
    'Laptops' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE category='Laptop'"))['count'],
    'Desktops' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE category='Desktop'"))['count'],
    'Monitors' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE category='Monitor'"))['count'],
    'Accessories' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE category='Accessories'"))['count'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kimaro Computers - Premium Computer Solutions in Tanzania</title>
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
            overflow-x: hidden;
        }
        
        /* Custom Swahili Calligraphy Font */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap');
        
        .swahili-text {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            letter-spacing: 1px;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            padding: 15px 5%;
            position: fixed;
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
        
        .nav-links a:hover:before {
            width: 100%;
        }
        
        .nav-links a:hover {
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
        
        /* Hero Section with Background Image */
       .hero {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('assets/css/electronick.jpg') no-repeat center center/cover fixed;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    margin-top: 0;
}
        .hero-content {
            max-width: 800px;
            padding: 20px;
            animation: fadeInUp 1s ease-out;
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
        
        .hero h1 {
            font-size: 64px;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero .swahili-text {
            font-size: 28px;
            color: #ffc107;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .hero p {
            font-size: 18px;
            margin-bottom: 35px;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #ffc107, #ff9800);
            color: #1e3c72;
            padding: 14px 35px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(255,193,7,0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255,193,7,0.4);
        }
        
        .btn-secondary {
            background: transparent;
            color: white;
            padding: 14px 35px;
            border: 2px solid white;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: white;
            color: #1e3c72;
            transform: translateY(-3px);
        }
        
        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 60px 5%;
            color: white;
        }
        
        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }
        
        .stat-item {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stat-number {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .stat-label {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Categories Section */
        .categories-section {
            padding: 80px 5%;
            background: #f8f9fc;
        }
        
        .section-title {
            text-align: center;
            font-size: 42px;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .section-subtitle {
            text-align: center;
            color: #7f8c8d;
            font-size: 18px;
            margin-bottom: 50px;
        }
        
        .categories-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .category-card {
            background: white;
            border-radius: 20px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            cursor: pointer;
        }
        
        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .category-icon {
            font-size: 60px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .category-card h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .category-count {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Products Section */
        .products-section {
            padding: 80px 5%;
            background: white;
        }
        
        .products-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .btn-view {
            width: 100%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 80px 5%;
            text-align: center;
            color: white;
        }
        
        .cta-content h2 {
            font-size: 42px;
            margin-bottom: 20px;
        }
        
        .cta-content p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .btn-cta {
            background: #ffc107;
            color: #1e3c72;
            padding: 14px 40px;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        /* Footer */
        .footer {
            background: #0a1a2e;
            color: white;
            padding: 60px 5% 30px;
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
            .hero h1 {
                font-size: 36px;
            }
            
            .hero .swahili-text {
                font-size: 20px;
            }
            
            .section-title {
                font-size: 32px;
            }
            
            .nav-links {
                display: none;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        /* Animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body style="">
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <i class="fas fa-microchip"></i>
                <span>Kimaro Computers</span>
            </a>
            <div class="nav-links">
                <a href="index.php">Nyumbani</a>
                <a href="products.php">Bidhaa</a>
                <a href="about.php">Kuhusu Sisi</a>
                <a href="contact.php" class="btn-contact">Wasiliana</a>
            </div>
        </div>
    </nav>
    
    <div class="hero">
        <div class="hero-content">
            <h1>Kimaro Computers</h1>
            <div class="swahili-text">"Teknolojia ya Kwanza, Ubora wa Kipekee"</div>
            <p>Tunatoa vifaa vya kompyuta vya kisasa, vya kuaminika na kwa bei nafuu. Kutoka laptops, desktops, monitors hadi accessories zote za kompyuta.</p>
            <div class="hero-buttons">
                <a href="products.php" class="btn-primary">Nunua Sasa</a>
                <a href="contact.php" class="btn-secondary">Wasiliana Nasi</a>
            </div>
        </div>
    </div>
    
    <div class="stats-section">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Wateja Walioridhika</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Bidhaa Zilizouzwa</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Msaada kwa Wateja</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Ubora Halisi</div>
            </div>
        </div>
    </div>
    
    <div class="categories-section">
        <h2 class="section-title">Madarasa ya Bidhaa</h2>
        <p class="section-subtitle">Chagua kutoka kwa madarasa yetu mbalimbali ya bidhaa za kompyuta</p>
        <div class="categories-grid">
            <div class="category-card" onclick="filterCategory('Laptop')">
                <div class="category-icon"><i class="fas fa-laptop"></i></div>
                <h3>Laptops</h3>
                <div class="category-count"><?php echo $categories['Laptops']; ?>+ Bidhaa</div>
            </div>
            <div class="category-card" onclick="filterCategory('Desktop')">
                <div class="category-icon"><i class="fas fa-desktop"></i></div>
                <h3>Desktops</h3>
                <div class="category-count"><?php echo $categories['Desktops']; ?>+ Bidhaa</div>
            </div>
            <div class="category-card" onclick="filterCategory('Monitor')">
                <div class="category-icon"><i class="fas fa-tv"></i></div>
                <h3>Monitors</h3>
                <div class="category-count"><?php echo $categories['Monitors']; ?>+ Bidhaa</div>
            </div>
            <div class="category-card" onclick="filterCategory('Accessories')">
                <div class="category-icon"><i class="fas fa-mouse"></i></div>
                <h3>Accessories</h3>
                <div class="category-count"><?php echo $categories['Accessories']; ?>+ Bidhaa</div>
            </div>
        </div>
    </div>
    
    <div class="products-section">
        <h2 class="section-title">Bidhaa Zetu Maarufu</h2>
        <p class="section-subtitle">Bidhaa bora zinazopendwa na wateja wetu</p>
        <div class="products-grid">
            <?php while($product = mysqli_fetch_assoc($featured)): ?>
            <div class="product-card" onclick="viewProduct(<?php echo $product['id']; ?>)">
                <?php if($product['featured']): ?>
                    <div class="product-badge">⭐ Maarufu</div>
                <?php endif; ?>
                <div class="product-image">
                    <?php if($product['image'] && file_exists("uploads/".$product['image'])): ?>
                        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div style="font-size: 64px; color: #bdc3c7;"><i class="fas fa-microchip"></i></div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="brand-badge"><?php echo htmlspecialchars($product['brand']); ?></div>
                    <div class="product-price">TZS <?php echo number_format($product['price']); ?></div>
                    <button class="btn-view" onclick="event.stopPropagation(); viewProduct(<?php echo $product['id']; ?>)">
                        Tazama Maelezo <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <div class="cta-section">
        <div class="cta-content">
            <h2>Unahitaji Msaada wa Kuchagua Bidhaa?</h2>
            <p>Timu yetu ya wataalam iko tayari kukusaidia kuchagua bidhaa inayokufaa zaidi</p>
            <a href="contact.php" class="btn-cta">Wasiliana Nasi Sasa</a>
        </div>
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
                <p><i class="fas fa-phone"></i> +255 782751622</p>
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
        
        function filterCategory(category) {
            window.location.href = 'products.php?category=' + category;
        }
        
        // Counter animation for stats
        const counters = document.querySelectorAll('.stat-number');
        const speed = 200;
        
        counters.forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.innerText);
                const count = parseInt(counter.innerText);
                const increment = target / speed;
                
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            
            updateCount();
        });
        
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>