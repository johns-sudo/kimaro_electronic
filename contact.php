<?php
require_once 'includes/config.php';

// Handle contact form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Tafadhali jaza sehemu zote zinazohitajika";
    } else {
        // Here you can save to database or send email
        // For now, just show success message
        $success = "Asante kwa ujumbe wako! Tutawasiliana nawe hivi karibuni.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wasiliana Nasi - Kimaro Computers</title>
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"> -->
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 5%;
        }
        
        /* Contact Grid */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        
        /* Contact Info Cards */
        .contact-info h2,
        .contact-form h2 {
            font-size: 32px;
            color: #1e3c72;
            margin-bottom: 15px;
        }
        
        .contact-info p {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .info-cards {
            display: grid;
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .info-card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .info-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .info-content h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .info-content p,
        .info-content a {
            color: #7f8c8d;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .info-content a:hover {
            color: #1e3c72;
        }
        
        /* Social & Contact Buttons */
        .contact-actions {
            margin-top: 30px;
        }
        
        .contact-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-whatsapp {
            background: #25D366;
            color: white;
            padding: 14px 25px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s;
            flex: 1;
            justify-content: center;
        }
        
        .btn-whatsapp:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(37,211,102,0.4);
        }
        
        .btn-call {
            background: #34B7F1;
            color: white;
            padding: 14px 25px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s;
            flex: 1;
            justify-content: center;
        }
        
        .btn-call:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(52,183,241,0.4);
        }
        
        .btn-email {
            background: #EA4335;
            color: white;
            padding: 14px 25px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            transition: all 0.3s;
            flex: 1;
            justify-content: center;
        }
        
        .btn-email:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(234,67,53,0.4);
        }
        
        /* Business Hours */
        .business-hours {
            background: white;
            padding: 25px;
            border-radius: 20px;
            margin-top: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .business-hours h3 {
            color: #1e3c72;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .hours-list {
            list-style: none;
        }
        
        .hours-list li {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .hours-list li:last-child {
            border-bottom: none;
        }
        
        .day {
            font-weight: 500;
            color: #2c3e50;
        }
        
        .time {
            color: #7f8c8d;
        }
        
        /* Contact Form */
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e3c72;
            box-shadow: 0 0 0 3px rgba(30,60,114,0.1);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 14px 30px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30,60,114,0.3);
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
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
        
        /* Map Section */
        .map-section {
            padding: 0 5% 60px;
        }
        
        .map-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
        
        /* Footer */
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
            
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .nav-links {
                display: none;
            }
            
            .contact-buttons {
                flex-direction: column;
            }
            
            .info-card {
                padding: 20px;
            }
            
            .contact-form {
                padding: 25px;
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
                <a href="about.php">Kuhusu Sisi</a>
                <a href="contact.php" class="active btn-contact-nav">Wasiliana</a>
            </div>
        </div>
    </nav>
    
    <div class="page-header">
        <h1>📞 Wasiliana Nasi</h1>
        <p>Tupate kuwasiliana na wewe kwa mahitaji yako yote ya vifaa vya kompyuta</p>
    </div>
    
    <div class="container">
        <div class="contact-grid">
            <!-- Left Side - Contact Information -->
            <div class="contact-info">
                <h2>Wasiliana Nasi</h2>
                <p>Tupo hapa kukusaidia. Chagua njia yoyote unayopendelea kuwasiliana nasi.</p>
                
                <div class="info-cards">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Anuani Yetu</h3>
                            <p>Dar es Salaam, Tanzania<br>kariakoo, mtaa wa magira na likoma</p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>Simu ya Mawasiliano</h3>
                            <p><a href="tel:+255782751622">+255 782 751 622</a></p>
                            <p><a href="tel:+255782751622">+255 782751622</a></p>
                        </div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>Barua Pepe</h3>
                            <p><a href="mailto:kimarojohn92@gmail.com">kimarojohn92@gmail.com</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Action Buttons -->
                <div class="contact-actions">
                    <div class="contact-buttons">
                        <a href="https://wa.me/255123456789?text=Habari%2C%20Nina%20swali%20kuhusu%20bidhaa%20za%20kompyuta" 
                           class="btn-whatsapp" target="_blank">
                            <i class="fab fa-whatsapp fa-lg"></i> 
                            WhatsApp
                        </a>
                        <a href="tel:+255123456789" class="btn-call">
                            <i class="fas fa-phone-alt fa-lg"></i> 
                            Piga Simu
                        </a>
                        <a href="mailto:kimarojohn92@gmail.com" class="btn-email">
                            <i class="fas fa-envelope fa-lg"></i> 
                            Tuma Barua
                        </a>
                    </div>
                </div>
                
                <!-- Business Hours -->
                <div class="business-hours">
                    <h3>
                        <i class="fas fa-clock"></i> 
                        Saa za Kufungua
                    </h3>
                    <ul class="hours-list">
                        <li>
                            <span class="day">Jumatatu - Ijumaa</span>
                            <span class="time">8:00 AM - 6:00 PM</span>
                        </li>
                        <li>
                            <span class="day">Jumamosi</span>
                            <span class="time">9:00 AM - 3:00 PM</span>
                        </li>
                        <li>
                            <span class="day">Jumapili & Likizo</span>
                            <span class="time">Imefungwa</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Right Side - Contact Form -->
            <div class="contact-form">
                <h2>Tuma Ujumbe</h2>
                <p style="margin-bottom: 25px; color: #7f8c8d;">Jaza fomu hapa chini na tutawasiliana nawe haraka iwezekanavyo.</p>
                
                <?php if($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Jina Kamili *</label>
                        <input type="text" name="name" placeholder="Ingiza jina lako" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Barua Pepe *</label>
                        <input type="email" name="email" placeholder="Ingiza barua pepe yako" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Namba ya Simu</label>
                        <input type="tel" name="phone" placeholder="Ingiza namba ya simu">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Mada</label>
                        <select name="subject">
                            <option value="">Chagua mada...</option>
                            <option value="Bidhaa">Maswali kuhusu Bidhaa</option>
                            <option value="Bei">Maswali kuhusu Bei</option>
                            <option value="Order">Kuagiza Bidhaa</option>
                            <option value="Ushauri">Ushauri wa Teknolojia</option>
                            <option value="Malalamiko">Malalamiko</option>
                            <option value="Nyingine">Nyingine</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment"></i> Ujumbe Wako *</label>
                        <textarea name="message" rows="5" placeholder="Andika ujumbe wako hapa..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Tuma Ujumbe
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Google Maps Section -->
    <div class="map-section">
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126915.52128993113!2d39.203729!3d-6.792354!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x185c4e8d2a6c4f5b%3A0x8c5f5e2d5e8c4a3d!2sDar%20es%20Salaam%2C%20Tanzania!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
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
                    <a href="https://wa.me/255782751622" target="_blank"><i class="fab fa-whatsapp"></i></a>
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
                <p><i class="fas fa-phone"></i> <a href="tel:+255782751622">+255 782 751 622</a></p>
                <p><i class="fab fa-whatsapp"></i> <a href="https://wa.me/255782751622" target="_blank">WhatsApp: +255 782751622</a></p>
                <p><i class="fas fa-envelope"></i> kimarojohn92@gmail.com</p>
                <p><i class="fas fa-map-marker-alt"></i> Dar es Salaam, Tanzania</p>
            </div>
            <div class="footer-section">
                <h3>Saa za Kufungua</h3>
                <p>Jumatatu - Ijumaa: 8:00 - 22:00</p>
                <p>Jumamosi: 8:00 - 22:00</p>
                <p>Jumapili: 9:00- 19:00</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Kimaro Computers. all right reserved. | designed by john kimaro </p>
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
        
        // WhatsApp click tracking (optional)
        document.querySelectorAll('.btn-whatsapp').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('WhatsApp button clicked');
                // You can add analytics here
            });
        });
    </script>
</body>
</html>