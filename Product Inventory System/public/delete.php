<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message('Invalid product ID', 'danger');
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);

// Get product name for confirmation
$product = get_product_by_id($conn, $id);
if (!$product) {
    set_message('Product not found', 'danger');
    header('Location: index.php');
    exit();
}

// Check if confirmed
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Delete product using prepared statement
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        set_message("Product '{$product['name']}' deleted successfully!", 'success');
        header('Location: index.php');
        exit();
    } else {
        set_message('Failed to delete product', 'danger');
        header('Location: index.php');
        exit();
    }
}

// Show confirmation page
require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h4>
            </div>
            <div class="card-body text-center">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <h4>Are you sure you want to delete this product?</h4>
                    <p class="lead"><strong><?php echo htmlspecialchars($product['name']); ?></strong></p>
                    
                    <div class="card mb-3">
                        <div class="card-body text-start">
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                            <p><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> units</p>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    This product still has <?php echo $product['stock_quantity']; ?> units in stock.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <p class="text-muted">This action cannot be undone.</p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="delete.php?id=<?php echo $id; ?>&confirm=yes" 
                           class="btn btn-danger btn-lg">
                            <i class="fas fa-trash"></i> Yes, Delete It
                        </a>
                        <a href="view.php?id=<?php echo $id; ?>" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left"></i> Back to Product List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>