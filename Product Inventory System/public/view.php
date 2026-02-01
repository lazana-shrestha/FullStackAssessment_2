<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_message('Invalid product ID', 'danger');
    header('Location: index.php');
    exit();
}

$id = intval($_GET['id']);
$product = get_product_by_id($conn, $id);

if (!$product) {
    set_message('Product not found', 'danger');
    header('Location: index.php');
    exit();
}

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Product Details</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo htmlspecialchars($product['name']); ?></h4>
                <div>
                    <?php echo check_stock($product['stock_quantity']); ?>
                    <span class="badge bg-secondary">ID: #<?php echo $product['id']; ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                 style="height: 200px;">
                                <i class="fas fa-box fa-4x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h5 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h5>
                        
                        <p class="mt-3">
                            <strong>Category:</strong> 
                            <span class="badge bg-info"><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></span>
                        </p>
                        
                        <p><strong>Stock Quantity:</strong> 
                            <?php if ($product['stock_quantity'] == 0): ?>
                                <span class="text-danger">Out of Stock</span>
                            <?php elseif ($product['stock_quantity'] < 10): ?>
                                <span class="text-warning"><?php echo $product['stock_quantity']; ?> (Low Stock)</span>
                            <?php else: ?>
                                <span class="text-success"><?php echo $product['stock_quantity']; ?> (In Stock)</span>
                            <?php endif; ?>
                        </p>
                        
                        <p><strong>Description:</strong></p>
                        <div class="border p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?>
                        </div>
                        
                        <div class="mt-4">
                            <p class="text-muted small">
                                <strong>Created:</strong> <?php echo date('F j, Y', strtotime($product['created_at'])); ?>
                                <?php if ($product['updated_at'] != $product['created_at']): ?>
                                    | <strong>Last Updated:</strong> <?php echo date('F j, Y', strtotime($product['updated_at'])); ?>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
                <?php if (is_admin()): ?>
                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Stock Information</h5>
            </div>
            <div class="card-body">
                <div class="alert 
                    <?php echo $product['stock_quantity'] == 0 ? 'alert-danger' : 
                           ($product['stock_quantity'] < 10 ? 'alert-warning' : 'alert-success'); ?>">
                    <h5 class="alert-heading">
                        <?php if ($product['stock_quantity'] == 0): ?>
                            <i class="fas fa-exclamation-triangle"></i> Out of Stock
                        <?php elseif ($product['stock_quantity'] < 10): ?>
                            <i class="fas fa-exclamation-circle"></i> Low Stock Alert
                        <?php else: ?>
                            <i class="fas fa-check-circle"></i> Stock Available
                        <?php endif; ?>
                    </h5>
                    <p class="mb-0">
                        Current Stock: <strong><?php echo $product['stock_quantity']; ?> units</strong>
                    </p>
                </div>
                
                <?php if (is_admin() && $product['stock_quantity'] < 10): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Recommendation:</strong>
                        Consider restocking this product soon.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (is_admin()): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="edit.php?id=<?php echo $product['id']; ?>" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                    <a href="delete.php?id=<?php echo $product['id']; ?>" 
                       class="btn btn-danger w-100"
                       onclick="return confirm('Are you sure you want to delete this product?')">
                        <i class="fas fa-trash"></i> Delete Product
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>