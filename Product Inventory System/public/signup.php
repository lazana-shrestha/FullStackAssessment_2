<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

// Set default role as 'user' for security
$default_role = 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Default to 'user'
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif (!in_array($role, ['admin', 'user'])) {
        $error = 'Invalid role selected';
    } else {
        // Check if username or email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $success = "Registration successful! You are registered as a " . $role . ". You can now login.";
                // Clear form
                $username = $email = '';
                $role = 'user'; // Reset to default
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus"></i> Create Account</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                               required maxlength="50">
                        <div class="form-text">Choose a unique username (max 50 characters)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Account Type *</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user" <?php echo (($role ?? 'user') == 'user') ? 'selected' : ''; ?>>
                                Regular User (View products only)
                            </option>
                            <option value="admin" <?php echo (($role ?? 'user') == 'admin') ? 'selected' : ''; ?>>
                                Administrator (Full access to add/edit/delete)
                            </option>
                        </select>
                        <div class="form-text">
                            <strong>Note:</strong> For testing, you can create an admin account. 
                            In a real system, admin accounts would require special approval.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">Minimum 6 characters</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Demo Accounts Available:</h6>
                        <ul class="mb-0">
                            <li><strong>Admin:</strong> username: <code>admin</code> | password: <code>password</code></li>
                            <li><strong>User:</strong> username: <code>user1</code> | password: <code>password</code></li>
                        </ul>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <hr>
                <p class="text-center">
                    Already have an account? 
                    <a href="login.php" class="text-decoration-none">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>