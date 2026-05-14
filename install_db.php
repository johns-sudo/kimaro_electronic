<?php
// Aiven MySQL Connection
$host = 'mysql-2505e7c2-kimarojohn92-71f0.h.aivencloud.com';
$port = 12713;
$user = 'avnadmin';
$pass = 'AVNS_gVgep3zs4IEnePVrO_0';
$dbname = 'kimaro_electronics';

echo "<h2>🔧 Installing Database Tables</h2>";

// Connect with SSL
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("❌ Connection failed: " . mysqli_connect_error());
}

echo "✅ Connected to database: " . $dbname . "<br><br>";

// Create users table
$sql1 = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql1)) {
    echo "✅ Users table created<br>";
} else {
    echo "❌ Users table error: " . mysqli_error($conn) . "<br>";
}

// Create products table
$sql2 = "CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    brand VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    stock INT DEFAULT 0,
    featured TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql2)) {
    echo "✅ Products table created<br>";
} else {
    echo "❌ Products table error: " . mysqli_error($conn) . "<br>";
}

// Create orders table
$sql3 = "CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    quantity INT DEFAULT 1,
    instructions TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql3)) {
    echo "✅ Orders table created<br>";
} else {
    echo "❌ Orders table error: " . mysqli_error($conn) . "<br>";
}

// Create product_specs table
$sql4 = "CREATE TABLE IF NOT EXISTS product_specs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    spec_name VARCHAR(100) NOT NULL,
    spec_value TEXT NOT NULL
)";
if (mysqli_query($conn, $sql4)) {
    echo "✅ Product specs table created<br>";
} else {
    echo "❌ Product specs error: " . mysqli_error($conn) . "<br>";
}

// Insert admin user
$sql5 = "INSERT INTO users (username, password, email, role) 
         SELECT 'admin', 'admin123', 'admin@kimarocomputers.com', 'admin'
         WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin')";
if (mysqli_query($conn, $sql5)) {
    echo "✅ Admin user created (admin / admin123)<br>";
} else {
    echo "❌ Admin user error: " . mysqli_error($conn) . "<br>";
}

// Show all tables
$result = mysqli_query($conn, "SHOW TABLES");
echo "<br><h3>📊 Tables in Database:</h3>";
if (mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_array($result)) {
        echo "<li>✅ " . $row[0] . "</li>";
    }
    echo "</ul>";
} else {
    echo "❌ No tables found!<br>";
}

mysqli_close($conn);
echo "<br><hr>";
echo "<h3>✅ Installation Complete!</h3>";
echo "<p>👉 <a href='admin/login.php'>Click here to login to Admin Panel</a></p>";
?>
