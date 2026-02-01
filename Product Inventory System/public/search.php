<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_login();

$page_title = "Advanced Search";

// Get categories for dropdown
$categories = get_categories($conn);

// Get search results if form submitted
$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $name = trim($_GET['name'] ?? '');
    $category = $_GET['category'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $max_price = $_GET['max_price'] ?? '';
    $min_stock = $_GET['min_stock'] ?? '';
    $max_stock = $_GET['max_stock'] ?? '';
    
    // Build query
    $query = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if (!empty($name)) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $name_term = "%$name%";
        $params[] = $name_term;
        $params[] = $name_term;
    }
    
    if (!empty($category)) {
        $query .= " AND category = ?";
        $params[] = $category;
    }
    
    if (!empty($min_price) && is_numeric($min_price)) {
        $query .= " AND price >= ?";
        $params[] = floatval($min_price);
    }
    
    if (!empty($max_price) && is_numeric($max_price)) {
        $query .= " AND price <= ?";
        $params[] = floatval($max_price);
    }
    
    if (!empty($min_stock) && is_numeric($min_stock)) {
        $query .= " AND stock_quantity >= ?";
        $params[] = intval($min_stock);
    }
    
    if (!empty($max_stock) && is_numeric($max_stock)) {
        $query .= " AND stock_quantity <= ?";
        $params[] = intval($max_stock);
    }
    
    $query .= " ORDER BY name ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}

require_once '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h1><i class="fas fa-search"></i> Advanced Product Search</h1>
        <p class="lead">Find products using multiple criteria</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-filter"></i> Search Criteria</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name/Description</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($_GET['name'] ?? ''); ?>"
                               placeholder="Enter name or description...">
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>"
                                    <?php echo (($_GET['category'] ?? '') == $cat) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_price" class="form-label">Min Price ($)</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" 
                                       value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>" 
                                       step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_price" class="form-label">Max Price ($)</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" 
                                       value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" 
                                       step="0.01" min="0" placeholder="1000.00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_stock" class="form-label">Min Stock</label>
                                <input type="number" class="form-control" id="min_stock" name="min_stock" 
                                       value="<?php echo htmlspecialchars($_GET['min_stock'] ?? ''); ?>" 
                                       min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_stock" class="form-label">Max Stock</label>
                                <input type="number" class="form-control" id="max_stock" name="max_stock" 
                                       value="<?php echo htmlspecialchars($_GET['max_stock'] ?? ''); ?>" 
                                       min="0" placeholder="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="search" value="1" class="btn btn-primary btn-lg">
                            <i class="fas fa-search"></i> Search Products
                        </button>
                        <a href="search.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear Form
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-list"></i> Search Results 
                    <?php if (isset($_GET['search'])): ?>
                        <span class="badge bg-primary float-end"><?php echo count($results); ?> found</span>
                    <?php endif; ?>
                </h4>
            </div>
            <div class="card-body">
                <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])): ?>
                    <?php if (empty($results)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No products found matching your criteria.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $product): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                <?php if ($product['stock_quantity'] == 0): ?>
                                                    <span class="badge bg-danger ms-2">Out of Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo check_stock($product['stock_quantity']); ?></td>
                                            <td>
                                                <a href="view.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center text-muted p-5">
                        <i class="fas fa-search fa-4x mb-3"></i>
                        <h4>Enter search criteria to find products</h4>
                        <p>Use the form to search by name, category, price range, or stock level.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>