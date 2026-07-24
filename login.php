<?php
/**
 * Login Page - TravenzoTravel
 */
$pageTitle = 'Login';
require_once 'includes/header.php';
$base = BASE_PATH;

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $oldEmail = $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if (empty($password)) {
            $errors[] = 'Password is required.';
        }

        if (empty($errors)) {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                if (!$user['is_active']) {
                    $errors[] = 'Your account has been deactivated. Contact support.';
                } else {
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];

                    setFlash('success', 'Welcome back, ' . $user['first_name'] . '!');

                    // Redirect to intended page
                    $redirect = $_SESSION['redirect_after_login'] ?? '/';
                    unset($_SESSION['redirect_after_login']);
                    redirect($redirect);
                }
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}
?>

<section class="auth-page">
    <div class="container">
        <div class="auth-wrapper">
            <!-- Left Panel -->
            <div class="auth-sidebar">
                <div class="auth-sidebar-content">
                    <i class="fas fa-sign-in-alt auth-big-icon"></i>
                    <h2>Welcome Back!</h2>
                    <p>Login to manage your bookings and access exclusive deals.</p>
                    <ul class="auth-perks">
                        <li><i class="fas fa-check"></i> View & manage your bookings</li>
                        <li><i class="fas fa-check"></i> Quick checkout with saved details</li>
                        <li><i class="fas fa-check"></i> Track refund status</li>
                        <li><i class="fas fa-check"></i> Personalized flight recommendations</li>
                        <li><i class="fas fa-check"></i> Exclusive member-only offers</li>
                    </ul>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="auth-form-panel">
                <h1>Login to Your Account</h1>
                <p class="auth-subtitle">Enter your credentials to continue</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base; ?>/login.php" class="auth-form" id="loginForm" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" placeholder="you@example.com" value="<?php echo $oldEmail; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="Enter your password" required>
                            <button type="button" class="toggle-pass" data-target="password"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="form-group form-inline-between">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn-auth-submit">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div class="auth-footer-link">
                    Don't have an account? <a href="<?php echo $base; ?>/register.php">Create one now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
