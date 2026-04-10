<?php
require_once 'includes/config.php';

// Get some stats for the about page
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$total_brands = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT brand) as count FROM products"))['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuhusu Sisi - Kimaro Computers</title>
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
        
        /* Navigation */
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
        
        .btn-contact-nav {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white !important;
            padding: 8px 20px;
            border-radius: 50px;
        }
        
        .btn-contact-nav:before {
            display: none;
        }
        
        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            background-image: url('assets/css/johnss.jpg');
            padding: 80px 5% 50px;
            color: white;
            text-align: center;
        }
        
        .page-header h1 {
            font-size: 48px;
            margin-bottom: 15px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        .page-header p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 700px;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 5%;
        }
        
        /* About Content */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            margin-bottom: 80px;
        }
        
        .about-text h2 {
            font-size: 36px;
            color: #1e3c72;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .about-text h2:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
        }
        
        .about-text .swahili-quote {
            font-size: 20px;
            font-style: italic;
            color: #ffc107;
            margin: 20px 0;
            font-family: 'Playfair Display', serif;
        }
        
        .about-text p {
            color: #555;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .about-image {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .about-image:hover img {
            transform: scale(1.05);
        }
        
        /* Mission & Vision */
        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 80px;
        }
        
        .mission-card, .vision-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .mission-card:hover, .vision-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .mission-icon, .vision-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: white;
            font-size: 32px;
        }
        
        .mission-card h3, .vision-card h3 {
            font-size: 24px;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .mission-card p, .vision-card p {
            color: #7f8c8d;
            line-height: 1.6;
        }
        
        /* Stats Section */
        .stats-about {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: 60px 5%;
            margin: 60px 0;
            border-radius: 20px;
        }
        
        .stats-grid-about {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            text-align: center;
        }
        
        .stat-item-about {
            color: white;
        }
        
        .stat-number-about {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .stat-label-about {
            font-size: 16px;
            opacity: 0.9;
        }
        
        /* Values Section */
        .values-section {
            margin-bottom: 80px;
        }
        
        .section-title {
            text-align: center;
            font-size: 36px;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .section-subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 50px;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .value-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .value-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .value-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ffc107, #ff9800);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #1e3c72;
            font-size: 28px;
        }
        
        .value-card h3 {
            font-size: 20px;
            color: #1e3c72;
            margin-bottom: 10px;
        }
        
        .value-card p {
            color: #7f8c8d;
            line-height: 1.6;
            font-size: 14px;
        }
        
        /* Team Section */
        .team-section {
            margin-bottom: 80px;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .team-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .team-image {
            height: 250px;
            background: linear-gradient(135deg, #e8eaf6, #c5cae9);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .team-image i {
            font-size: 80px;
            color: #1e3c72;
        }
        
        .team-info {
            padding: 20px;
        }
        
        .team-info h3 {
            font-size: 20px;
            color: #1e3c72;
            margin-bottom: 5px;
        }
        
        .team-info p {
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .team-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .team-social a {
            width: 35px;
            height: 35px;
            background: #f0f2f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e3c72;
            transition: all 0.3s;
        }
        
        .team-social a:hover {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            transform: translateY(-3px);
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
            .page-header h1 {
                font-size: 32px;
            }
            
            .about-content {
                grid-template-columns: 1fr;
            }
            
            .mission-vision {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                display: none;
            }
            
            .stats-grid-about {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .values-grid {
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
                <a href="products.php">Bidhaa</a>
                <a href="about.php" class="active">Kuhusu Sisi</a>
                <a href="contact.php" class="btn-contact-nav">Wasiliana</a>
            </div>
        </div>
    </nav>
    
    <div class="page-header">
        <h1>🏢 Kuhusu Kimaro Computers</h1>
        <p>Kuanzia mwanzo hadi leo, tunajivunia kutoa bidhaa bora na huduma ya kipekee kwa wateja wetu</p>
    </div>
    
    <div class="container">
        <!-- About Story -->
        <div class="about-content">
            <div class="about-text">
                <div class="swahili-quote">
                    <i class="fas fa-quote-left"></i> "Teknolojia inayowezesha, Ubora unaoaminika"
                </div>
                <p>Kimaro Computers ilianzishwa mwaka 2023 kwa lengo la kutoa vifaa vya kompyuta vya ubora wa juu kwa bei nafuu kwa Watanzania. Tunaamini kuwa kila Mtanzania anastahili kupata teknolojia bora inayowezesha maisha na kazi zao.</p>
                <p>Kuanzia siku za kwanza, tumekuwa tukijitahidi kuwaletea wateja wetu bidhaa za kisasa kutoka kwa chapa maarufu duniani. Leo, tunajivunia kuwa moja ya vituo vinavyoaminika zaidi vya vifaa vya kompyuta nchini Tanzania.</p>
                <p>Timu yetu ina wataalam wenye uzoefu zaidi ya miaka 10 katika sekta ya teknolojia, wakiwa tayari kukusaidia kuchagua bidhaa inayokufaa zaidi.</p>
            </div>
            <div class="about-image">
                <img src="assets/css/electronick.jpg" alt="Kimaro Computers Store">
            </div>
        </div>
        
        <!-- Mission & Vision -->
        <div class="mission-vision">
            <div class="mission-card">
                <div class="mission-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Dhamira Yetu</h3>
                <p>Kutoa vifaa vya kompyuta vya ubora wa juu na huduma bora kwa wateja wetu, kwa bei nafuu na kwa urahisi, ili kuwawezesha Watanzania kufikia teknolojia ya kisasa.</p>
            </div>
            <div class="vision-card">
                <div class="vision-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Maono Yetu</h3>
                <p>Kuwa kituo kikuu cha vifaa vya kompyuta nchini Tanzania, kinachotambulika kwa ubora, uaminifu na huduma bora kwa wateja.</p>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-about">
            <div class="stats-grid-about">
                <div class="stat-item-about">
                    <div class="stat-number-about"><?php echo $total_products; ?>+</div>
                    <div class="stat-label-about">Bidhaa Mbalimbali</div>
                </div>
                <div class="stat-item-about">
                    <div class="stat-number-about"><?php echo $total_brands; ?>+</div>
                    <div class="stat-label-about">Chapa Maarufu</div>
                </div>
                <div class="stat-item-about">
                    <div class="stat-number-about"><?php echo $total_orders; ?>+</div>
                    <div class="stat-label-about">Wateja Walioridhika</div>
                </div>
                <div class="stat-item-about">
                    <div class="stat-number-about">24/7</div>
                    <div class="stat-label-about">Msaada kwa Wateja</div>
                </div>
            </div>
        </div>
        
        <!-- Core Values -->
        <div class="values-section">
            <h2 class="section-title">Kanuni Zetu</h2>
            <p class="section-subtitle">Tunajikita kwenye kanuni hizi katika kila tunachofanya</p>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Ubora wa Kwanza</h3>
                    <p>Tunatoa bidhaa za ubora wa juu kutoka kwa chapa zinazoaminika duniani.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Uaminifu</h3>
                    <p>Tunawajibika kwa wateja wetu na tunathamini uaminifu wao.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Huduma bora</h3>
                    <p>Tunawapa wateja wetu huduma ya haraka na ya kitaalamu.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Ushirikiano</h3>
                    <p>Tunafanya kazi kwa ushirikiano na wateja, wafanyakazi na washirika wetu.</p>
                </div>
            </div>
        </div>
        
        <!-- Team Section -->
        <div class="team-section">
            <h2 class="section-title">Timu Yetu</h2>
            <p class="section-subtitle">Wataalamu wetu wako tayari kukusaidia</p>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="team-info">
                        <h3>Kimaro </h3>
                        <p>Founder & CEO</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <!-- <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="team-info">
                        <h3>Crespo</h3>
                        <p>Operations Manager</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="team-info">
                        <h3>Lawi Rashid</h3>
                        <p>Technical Specialist</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-card">
                    <div class="team-image">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="team-info">
                        <h3>robeth</h3>
                        <p>Customer Support</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div> -->
                <!-- </div>
            </div> -->
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
                <p><i class="fab fa-whatsapp"></i> +255 7682751622</p>
                <p><i class="fas fa-envelope"></i> kimarojohn82@gmail.com</p>
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
        
        // Counter animation for stats
        const counters = document.querySelectorAll('.stat-number-about');
        const speed = 200;
        
        counters.forEach(counter => {
            const target = parseInt(counter.innerText);
            let count = 0;
            const increment = target / speed;
            
            const updateCount = () => {
                if (count < target) {
                    count = Math.ceil(count + increment);
                    counter.innerText = count;
                    setTimeout(updateCount, 20);
                } else {
                    counter.innerText = target;
                }
            };
            
            updateCount();
        });
    </script>
</body>
</html>