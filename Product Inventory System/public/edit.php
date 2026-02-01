<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$page_title = "Edit Product";

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message('Invalid product ID', 'danger');
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);
$error = '';
$success = '';

// Get current product data
$product = get_product_by_id($conn, $id);
if (!$product) {
    set_message('Product not found', 'danger');
    header('Location: index.php');
    exit();
}

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
        // Update product using prepared statement
        $stmt = $conn->prepare("
            UPDATE products 
            SET name = ?, description = ?, price = ?, category = ?, 
                stock_quantity = ?, image_url = ? 
            WHERE id = ?
        ");
        
        if ($stmt->execute([$name, $description, $price, $category, $stock_quantity, $image_url, $id])) {
            set_message("Product '$name' updated successfully!", 'success');
            header('Location: view.php?id=' . $id);
            exit();
        } else {
            $error = 'Failed to update product. Please try again.';
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
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Product: <?php echo htmlspecialchars($product['name']); ?></h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($) *</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?php echo htmlspecialchars($product['price']); ?>" 
                                       step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                       value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" 
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
                                    <?php echo ($product['category'] == $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Image URL (Optional)</label>
                        <input type="url" class="form-control" id="image_url" name="image_url" 
                               value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>" 
                               placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                        <a href="view.php?id=<?php echo $id; ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>