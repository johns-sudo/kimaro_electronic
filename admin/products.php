<?php
require_once '../includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get brands function - renamed to avoid conflict with config.php
function getProductBrands() {
    $brands = [
        ['name' => 'HP'], ['name' => 'Dell'], ['name' => 'Lenovo'], 
        ['name' => 'Apple'], ['name' => 'ASUS'], ['name' => 'Acer'], 
        ['name' => 'MSI'], ['name' => 'Samsung'], ['name' => 'Logitech'], 
        ['name' => 'Razer'], ['name' => 'Corsair'], ['name' => 'Microsoft']
    ];
    return $brands;
}

$brands = getProductBrands();

// Add Product
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock = intval($_POST['stock']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    
    $sql = "INSERT INTO products (name, category, brand, price, description, image, stock, featured) 
            VALUES ('$name', '$category', '$brand', '$price', '$description', '$image', '$stock', '$featured')";
    
    if (mysqli_query($conn, $sql)) {
        $product_id = mysqli_insert_id($conn);
        
        // Save specifications
        if (isset($_POST['spec_name']) && is_array($_POST['spec_name'])) {
            for($i = 0; $i < count($_POST['spec_name']); $i++) {
                $spec_name = mysqli_real_escape_string($conn, $_POST['spec_name'][$i]);
                $spec_value = mysqli_real_escape_string($conn, $_POST['spec_value'][$i]);
                if(!empty($spec_name) && !empty($spec_value)) {
                    $spec_sql = "INSERT INTO product_specs (product_id, spec_name, spec_value) 
                                 VALUES ('$product_id', '$spec_name', '$spec_value')";
                    mysqli_query($conn, $spec_sql);
                }
            }
        }
        
        echo "<script>alert('Product added successfully!'); window.location.href='products.php';</script>";
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Edit Product
if (isset($_POST['edit_product'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock = intval($_POST['stock']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $image = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        
        $sql = "UPDATE products SET name='$name', category='$category', brand='$brand', price='$price', 
                description='$description', image='$image', stock='$stock', featured='$featured' 
                WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', category='$category', brand='$brand', price='$price', 
                description='$description', stock='$stock', featured='$featured' 
                WHERE id=$id";
    }
    
    if (mysqli_query($conn, $sql)) {
        // Delete existing specs
        mysqli_query($conn, "DELETE FROM product_specs WHERE product_id=$id");
        
        // Save new specifications
        if (isset($_POST['spec_name']) && is_array($_POST['spec_name'])) {
            for($i = 0; $i < count($_POST['spec_name']); $i++) {
                $spec_name = mysqli_real_escape_string($conn, $_POST['spec_name'][$i]);
                $spec_value = mysqli_real_escape_string($conn, $_POST['spec_value'][$i]);
                if(!empty($spec_name) && !empty($spec_value)) {
                    $spec_sql = "INSERT INTO product_specs (product_id, spec_name, spec_value) 
                                 VALUES ('$id', '$spec_name', '$spec_value')";
                    mysqli_query($conn, $spec_sql);
                }
            }
        }
        
        echo "<script>alert('Product updated successfully!'); window.location.href='products.php';</script>";
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM products WHERE id=$id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='products.php';</script>";
        exit();
    } else {
        $error = "Error deleting product";
    }
}

// Get product for editing with specs
$edit_product = null;
$edit_specs = [];
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
    $edit_product = mysqli_fetch_assoc($result);
    
    $specs_result = mysqli_query($conn, "SELECT * FROM product_specs WHERE product_id=$id");
    while($spec = mysqli_fetch_assoc($specs_result)) {
        $edit_specs[] = $spec;
    }
}

// Get all products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products - Kimaro Computers</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f7fa;
        }
        
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 100;
        }
        
        .sidebar h3 {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 20px;
        }
        
        .sidebar a {
            display: block;
            padding: 14px 25px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            border-left-color: #ffc107;
            padding-left: 30px;
        }
        
        .main {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .header h2 {
            color: #2c3e50;
        }
        
        .btn-add {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: transform 0.2s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 220px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.05);
        }
        
        .product-info {
            padding: 18px;
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
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .product-price {
            color: #27ae60;
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .product-stock {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 12px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-edit, .btn-delete, .btn-view {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-view {
            background: #3498db;
            color: white;
        }
        
        .btn-edit {
            background: #f39c12;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-view:hover, .btn-edit:hover, .btn-delete:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            background: white;
            width: 90%;
            max-width: 700px;
            margin: 40px auto;
            border-radius: 15px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-header h3 {
            font-size: 20px;
        }
        
        .modal-body {
            padding: 25px;
            max-height: 60vh;
            overflow-y: auto;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }
        
        .spec-row {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            align-items: center;
        }
        
        .spec-row input:first-child {
            width: 35%;
        }
        
        .spec-row input:last-child {
            width: 55%;
        }
        
        .remove-spec {
            background: #e74c3c;
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .add-spec {
            background: #27ae60;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 13px;
        }
        
        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #e1e5e9;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        
        .btn-save {
            background: linear-gradient(135deg, #27ae60, #229954);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .spec-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .spec-section h4 {
            margin-bottom: 12px;
            color: #2c3e50;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                left: -260px;
            }
            .main {
                margin-left: 0;
            }
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>⚡ Kimaro Computers</h3>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="products.php" class="active">📦 Products</a>
        <a href="orders.php">🛒 Orders</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
    
    <div class="main">
        <div class="header">
            <h2>📦 Products Management</h2>
            <button class="btn-add" onclick="openAddModal()">+ Add New Product</button>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="products-grid">
            <?php while($product = mysqli_fetch_assoc($products)): ?>
            <div class="product-card" onclick="viewProduct(<?php echo $product['id']; ?>)">
                <div class="product-image">
                    <?php if($product['image'] && file_exists("../uploads/".$product['image'])): ?>
                        <img src="../uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    <?php else: ?>
                        <div style="font-size: 64px; color: #bdc3c7;">🖥️</div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <span class="brand-badge"><?php echo htmlspecialchars($product['brand']); ?></span>
                    <div class="product-price">TZS <?php echo number_format($product['price']); ?></div>
                    <div class="product-stock">📦 Stock: <?php echo $product['stock']; ?> units</div>
                    <div class="product-actions">
                        <a href="?edit=<?php echo $product['id']; ?>" class="btn-edit" onclick="event.stopPropagation()">✏️ Edit</a>
                        <a href="?delete=<?php echo $product['id']; ?>" class="btn-delete" onclick="event.stopPropagation(); return confirm('Delete this product?')">🗑️ Delete</a>
                        <button class="btn-view" onclick="event.stopPropagation(); viewProduct(<?php echo $product['id']; ?>)">👁️ View</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    
    <!-- Add Product Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➕ Add New Product</h3>
            </div>
            <form method="POST" enctype="multipart/form-data" id="addProductForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Brand *</label>
                        <select name="brand" required>
                            <option value="">Select Brand</option>
                            <?php foreach($brands as $brand): ?>
                                <option value="<?php echo $brand['name']; ?>"><?php echo $brand['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <option value="Laptop">💻 Laptop</option>
                            <option value="Desktop">🖥️ Desktop</option>
                            <option value="Monitor">📺 Monitor</option>
                            <option value="Mouse">🐭 Mouse</option>
                            <option value="Keyboard">⌨️ Keyboard</option>
                            <option value="Accessories">🔌 Accessories</option>
                            <option value="Gaming">🎮 Gaming</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Price (TZS) *</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="spec-section">
                        <h4>⚙️ Product Specifications / Features</h4>
                        <div id="specsContainer">
                            <div class="spec-row">
                                <input type="text" name="spec_name[]" placeholder="Specification">
                                <input type="text" name="spec_value[]" placeholder="Value">
                                <button type="button" class="remove-spec" onclick="removeSpec(this)">✖</button>
                            </div>
                        </div>
                        <button type="button" class="add-spec" onclick="addSpecField()">+ Add More Specifications</button>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" accept="image/*">
                        <small>Supported: JPG, PNG, GIF</small>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" value="1"> ⭐ Featured Product
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" name="add_product" class="btn-save">Save Product</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <?php if($edit_product): ?>
    <div id="editModal" class="modal" style="display: block;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✏️ Edit Product</h3>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Product Name *</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($edit_product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Brand *</label>
                        <select name="brand" required>
                            <option value="">Select Brand</option>
                            <?php foreach($brands as $brand): ?>
                                <option value="<?php echo $brand['name']; ?>" <?php echo $edit_product['brand'] == $brand['name'] ? 'selected' : ''; ?>>
                                    <?php echo $brand['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="Laptop" <?php echo $edit_product['category'] == 'Laptop' ? 'selected' : ''; ?>>💻 Laptop</option>
                            <option value="Desktop" <?php echo $edit_product['category'] == 'Desktop' ? 'selected' : ''; ?>>🖥️ Desktop</option>
                            <option value="Monitor" <?php echo $edit_product['category'] == 'Monitor' ? 'selected' : ''; ?>>📺 Monitor</option>
                            <option value="Mouse" <?php echo $edit_product['category'] == 'Mouse' ? 'selected' : ''; ?>>🐭 Mouse</option>
                            <option value="Keyboard" <?php echo $edit_product['category'] == 'Keyboard' ? 'selected' : ''; ?>>⌨️ Keyboard</option>
                            <option value="Accessories" <?php echo $edit_product['category'] == 'Accessories' ? 'selected' : ''; ?>>🔌 Accessories</option>
                            <option value="Gaming" <?php echo $edit_product['category'] == 'Gaming' ? 'selected' : ''; ?>>🎮 Gaming</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Price (TZS) *</label>
                        <input type="number" name="price" value="<?php echo $edit_product['price']; ?>" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" value="<?php echo $edit_product['stock']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?php echo htmlspecialchars($edit_product['description']); ?></textarea>
                    </div>
                    
                    <div class="spec-section">
                        <h4>⚙️ Product Specifications / Features</h4>
                        <div id="editSpecsContainer">
                            <?php if(count($edit_specs) > 0): ?>
                                <?php foreach($edit_specs as $spec): ?>
                                <div class="spec-row">
                                    <input type="text" name="spec_name[]" value="<?php echo htmlspecialchars($spec['spec_name']); ?>" placeholder="Specification">
                                    <input type="text" name="spec_value[]" value="<?php echo htmlspecialchars($spec['spec_value']); ?>" placeholder="Value">
                                    <button type="button" class="remove-spec" onclick="removeSpec(this)">✖</button>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="spec-row">
                                    <input type="text" name="spec_name[]" placeholder="Specification">
                                    <input type="text" name="spec_value[]" placeholder="Value">
                                    <button type="button" class="remove-spec" onclick="removeSpec(this)">✖</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="add-spec" onclick="addEditSpecField()">+ Add More Specifications</button>
                    </div>
                    
                    <div class="form-group">
                        <label>Product Image (Leave empty to keep current)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if($edit_product['image']): ?>
                            <small>Current: <?php echo $edit_product['image']; ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" value="1" <?php echo $edit_product['featured'] ? 'checked' : ''; ?>> ⭐ Featured Product
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="products.php" class="btn-cancel" style="text-decoration: none;">Cancel</a>
                    <button type="submit" name="edit_product" class="btn-save">Update Product</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script>
        // View product function
        function viewProduct(id) {
            window.location.href = 'product_detail.php?id=' + id;
        }
        
        // Open Add Modal
        function openAddModal() {
            var modal = document.getElementById('addModal');
            if (modal) {
                modal.style.display = 'flex';
                var form = document.getElementById('addProductForm');
                if (form) {
                    form.reset();
                }
            }
        }
        
        // Close Add Modal
        function closeAddModal() {
            var modal = document.getElementById('addModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }
        
        // Add specification field for add form
        function addSpecField() {
            var container = document.getElementById('specsContainer');
            if (container) {
                var newRow = document.createElement('div');
                newRow.className = 'spec-row';
                newRow.innerHTML = '<input type="text" name="spec_name[]" placeholder="Specification"><input type="text" name="spec_value[]" placeholder="Value"><button type="button" class="remove-spec" onclick="removeSpec(this)">✖</button>';
                container.appendChild(newRow);
            }
        }
        
        // Add specification field for edit form
        function addEditSpecField() {
            var container = document.getElementById('editSpecsContainer');
            if (container) {
                var newRow = document.createElement('div');
                newRow.className = 'spec-row';
                newRow.innerHTML = '<input type="text" name="spec_name[]" placeholder="Specification"><input type="text" name="spec_value[]" placeholder="Value"><button type="button" class="remove-spec" onclick="removeSpec(this)">✖</button>';
                container.appendChild(newRow);
            }
        }
        
        // Remove specification field
        function removeSpec(button) {
            button.parentElement.remove();
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            var addModal = document.getElementById('addModal');
            var editModal = document.getElementById('editModal');
            if (event.target == addModal) {
                addModal.style.display = 'none';
            }
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>