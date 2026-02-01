<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

$page_title = "Product Inventory";

// Get search parameters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if (!empty($min_price)) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
}

if (!empty($max_price)) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
}

$query .= " ORDER BY created_at DESC";

// Execute query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get categories for filter
$categories = get_categories($conn);

require_once '../includes/header.php';
?>

<!-- Welcome Header - NO DUPLICATE BUTTON -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-boxes"></i> Product Inventory</h1>
                <p class="lead mb-0">
                    Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! 
                    <span class="badge bg-<?php echo ($_SESSION['role'] === 'admin') ? 'danger' : 'info'; ?> ms-2">
                        <?php echo ucfirst($_SESSION['role']); ?>
                    </span>
                </p>
            </div>
            <div class="text-muted small">
                <i class="fas fa-clock"></i> <?php echo date('F j, Y, g:i a'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Products</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <select class="form-control" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" 
                            <?php echo ($category == $cat) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="min_price" placeholder="Min Price" 
                       value="<?php echo htmlspecialchars($min_price); ?>" step="0.01">
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control" name="max_price" placeholder="Max Price" 
                       value="<?php echo htmlspecialchars($max_price); ?>" step="0.01">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
                <?php if ($search || $category || $min_price || $max_price): ?>
                    <a href="index.php" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="fas fa-times"></i> Clear Filters
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> Product List</h5>
        <span class="badge bg-info"><?php echo count($products); ?> products found</span>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No products found. 
                <?php if (is_admin()): ?>
                    <a href="add.php" class="alert-link">Add your first product</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?php echo $product['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                    <?php if ($product['stock_quantity'] == 0): ?>
                                        <span class="badge bg-danger ms-2">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td>
                                    <?php if ($product['stock_quantity'] < 10): ?>
                                        <span class="stock-low"><?php echo $product['stock_quantity']; ?></span>
                                    <?php else: ?>
                                        <span class="stock-ok"><?php echo $product['stock_quantity']; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo check_stock($product['stock_quantity']); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="view.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (is_admin()): ?>
                                            <a href="edit.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="delete.php?id=<?php echo $product['id']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this product?')" 
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Stats for Admin -->
<?php if (is_admin() && !empty($products)): ?>
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5><i class="fas fa-box"></i> Total Products</h5>
                <h3><?php echo count($products); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5><i class="fas fa-check-circle"></i> In Stock</h5>
                <h3><?php 
                    $in_stock = array_filter($products, function($p) { return $p['stock_quantity'] > 0; });
                    echo count($in_stock);
                ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body text-center">
                <h5><i class="fas fa-exclamation-triangle"></i> Low Stock</h5>
                <h3><?php 
                    $low_stock = array_filter($products, function($p) { 
                        return $p['stock_quantity'] > 0 && $p['stock_quantity'] < 10; 
                    });
                    echo count($low_stock);
                ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body text-center">
                <h5><i class="fas fa-times-circle"></i> Out of Stock</h5>
                <h3><?php 
                    $out_of_stock = array_filter($products, function($p) { return $p['stock_quantity'] == 0; });
                    echo count($out_of_stock);
                ?></h3>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>