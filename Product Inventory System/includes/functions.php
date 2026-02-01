<?php
/**
 * Set a flash message
 */
function set_message($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin - FIXED VERSION
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Require login - redirect if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['message'] = 'Please login to access this page';
        $_SESSION['message_type'] = 'danger';
        header('Location: login.php');
        exit();
    }
}

/**
 * Require admin - redirect if not admin - FIXED VERSION
 */
function require_admin() {
    require_login();
    
    if (!is_admin()) {
        $_SESSION['message'] = 'Access denied. Admin privileges required.';
        $_SESSION['message_type'] = 'danger';
        header('Location: index.php');
        exit();
    }
}

/**
 * Get all products
 */
function get_all_products($conn, $limit = 100) {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get product by ID
 */
function get_product_by_id($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get distinct categories
 */
function get_categories($conn) {
    $stmt = $conn->prepare("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Check stock availability
 */
function check_stock($quantity) {
    if ($quantity == 0) {
        return '<span class="badge bg-danger">Out of Stock</span>';
    } elseif ($quantity < 10) {
        return '<span class="badge bg-warning">Low Stock</span>';
    } else {
        return '<span class="badge bg-success">In Stock</span>';
    }
}
?>