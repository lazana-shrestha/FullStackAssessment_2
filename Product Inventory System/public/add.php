<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin - FIXED
if (!is_admin()) {
    $_SESSION['message'] = 'Access denied. Admin privileges required.';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit();
}

$page_title = "Add New Product";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category = trim($_POST['category']);
    $stock_quantity = $_POST['stock_quantity'];
    $image_url = trim($_POST['image_url']);
    
    // Validation
    if (empty($name) || empty($price) || !is_numeric($price)) {
        $error = 'Product name and valid price are required';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0';
    } elseif (!is_numeric($stock_quantity) || $stock_quantity < 0) {
        $error = 'Stock quantity must be a non-negative number';
    } else {
        // Insert product using prepared statement
        $stmt = $conn->prepare("
            INSERT INTO products (name, description, price, category, stock_quantity, image_url) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$name, $description, $price, $category, $stock_quantity, $image_url])) {
            $product_id = $conn->lastInsertId();
            $_SESSION['message'] = "Product '$name' added successfully!";
            $_SESSION['message_type'] = 'success';
            header('Location: view.php?id=' . $product_id);
            exit();
        } else {
            $error = 'Failed to add product. Please try again.';
        }
    }
}

// Get categories for dropdown
$categories = get_categories($conn);

require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Add New Product</h4>
                <small class="opacity-75">Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)</small>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($) *</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" 
                                       step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                       value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? '0'); ?>" 
                                       min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"
                                    <?php echo (($_POST['category'] ?? '') == $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Or enter a new category</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL (Optional)</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" 
                               value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Add Product
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>