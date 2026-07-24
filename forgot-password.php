<?php
/**
 * Forgot Password Page - TravenzoTravel
 */
$pageTitle = 'Forgot Password';
require_once 'includes/header.php';
$base = BASE_PATH;

if (isLoggedIn()) redirect('/');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $errors[] = 'Invalid submission.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($errors)) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            // Always show success (prevent email enumeration)
            $success = true;
        }
    }
}
?>

<section class="page-banner">
    <div class="container">
        <h1>Forgot <span>Password</span></h1>
        <p>We'll send you a link to reset your password</p>
    </div>
</section>

<section class="auth-page">
    <div class="container">
        <div class="forgot-wrapper">
            <?php if ($success): ?>
            <div class="confirm-box" style="max-width:500px;margin:0 auto;">
                <div class="confirm-icon"><i class="fas fa-envelope"></i></div>
                <h2>Check Your Email</h2>
                <p>If an account with that email exists, we've sent a password reset link. Please check your inbox and spam folder.</p>
                <a href="<?php echo $base; ?>/login.php" class="btn-primary" style="margin-top:20px;">Back to Login</a>
            </div>
            <?php else: ?>
            <div class="forgot-form-box">
                <div class="forgot-icon"><i class="fas fa-lock"></i></div>
                <h2>Reset Your Password</h2>
                <p>Enter the email address associated with your account.</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base; ?>/forgot-password.php" novalidate>
                    <?php echo csrfField(); ?>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" placeholder="you@example.com" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-auth-submit">
                        <i class="fas fa-paper-plane"></i> Send Reset Link
                    </button>
                </form>

                <div class="auth-footer-link">
                    Remember your password? <a href="<?php echo $base; ?>/login.php">Login here</a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
