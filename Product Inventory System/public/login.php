<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';
$username_input = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $username_input = htmlspecialchars($username);
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        // Check user using prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct - set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Set success message
                $_SESSION['message'] = 'Login successful! Welcome ' . $user['username'];
                $_SESSION['message_type'] = 'success';
                
                // Redirect to home page
                header('Location: index.php');
                exit();
            } else {
                // Password is incorrect
                $error = 'Invalid password';
            }
        } else {
            // User not found
            $error = 'Invalid username or email';
        }
    }
}

// Include header
require_once '../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Login to Product Inventory</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['signup_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['signup_success']); ?>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username or Email Address
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo $username_input; ?>" required
                               placeholder="Enter your username or email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required
                               placeholder="Enter your password">
                        <div class="form-text">
                            <a href="#" class="text-decoration-none" id="togglePassword">
                                <i class="fas fa-eye"></i> Show Password
                            </a>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </div>
                </form>
                
                <hr>
                
                <div class="text-center">
                    <p class="mb-2">
                        Don't have an account? 
                        <a href="signup.php" class="text-decoration-none fw-bold">Sign up here</a>
                    </p>
                    
                    <div class="card border-info mt-3">
                        <div class="card-header bg-info text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-user-shield"></i> Demo Accounts (Click to auto-fill)</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <button type="button" class="btn btn-outline-dark w-100 demo-btn" 
                                            data-username="admin" data-password="password">
                                        <i class="fas fa-crown"></i> Admin Account
                                        <div class="small">
                                            <code>admin</code> / <code>password</code>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-dark w-100 demo-btn" 
                                            data-username="user1" data-password="password">
                                        <i class="fas fa-user"></i> User Account
                                        <div class="small">
                                            <code>user1</code> / <code>password</code>
                                        </div>
                                    </button>
                                </div>
                            </div>
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fas fa-info-circle"></i> Both accounts use the same password: <code>password</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for demo accounts and password toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function(e) {
            e.preventDefault();
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i> Show Password' : '<i class="fas fa-eye-slash"></i> Hide Password';
        });
    }
    
    // Demo account buttons
    const demoButtons = document.querySelectorAll('.demo-btn');
    demoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const username = this.getAttribute('data-username');
            const password = this.getAttribute('data-password');
            
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            
            // Highlight the selected button
            demoButtons.forEach(btn => btn.classList.remove('btn-primary'));
            this.classList.add('btn-primary');
            
            // Show feedback
            const form = document.getElementById('loginForm');
            const feedback = document.createElement('div');
            feedback.className = 'alert alert-info mt-2';
            feedback.innerHTML = `<i class="fas fa-magic"></i> Demo account filled! Click "Login" to continue.`;
            
            // Remove any existing feedback
            const oldFeedback = document.querySelector('.alert-info');
            if (oldFeedback) oldFeedback.remove();
            
            form.parentNode.insertBefore(feedback, form.nextSibling);
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<?php require_once '../includes/footer.php'; ?>