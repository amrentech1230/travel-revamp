<?php
/**
 * Registration Page - TravenzoTravel
 */
$pageTitle = 'Create Account';
require_once 'includes/header.php';
$base = BASE_PATH;

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('/');
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $old['first_name'] = $firstName = sanitize($_POST['first_name'] ?? '');
        $old['last_name']  = $lastName  = sanitize($_POST['last_name'] ?? '');
        $old['email']      = $email     = sanitize($_POST['email'] ?? '');
        $old['phone']      = $phone     = sanitize($_POST['phone'] ?? '');
        $password    = $_POST['password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        // Validate
        if (empty($firstName)) $errors[] = 'First name is required.';
        if (empty($lastName))  $errors[] = 'Last name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirmPass) $errors[] = 'Passwords do not match.';
        if (empty($_POST['agree_terms'])) $errors[] = 'You must agree to the Terms & Conditions.';

        // Check duplicate email
        if (empty($errors)) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with this email already exists.';
            }
        }

        // Create account
        if (empty($errors)) {
            $db = getDB();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $phone, $hash]);

            $userId = $db->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            $_SESSION['user_email'] = $email;

            setFlash('success', 'Account created successfully! Welcome to TravenzoTravel.');
            redirect('/');
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
                    <i class="fas fa-user-plus auth-big-icon"></i>
                    <h2>Join TravenzoTravel</h2>
                    <p>Create your free account and unlock exclusive benefits.</p>
                    <ul class="auth-perks">
                        <li><i class="fas fa-check"></i> Access exclusive member deals</li>
                        <li><i class="fas fa-check"></i> Faster checkout with saved info</li>
                        <li><i class="fas fa-check"></i> Manage bookings in one place</li>
                        <li><i class="fas fa-check"></i> Instant e-ticket confirmations</li>
                        <li><i class="fas fa-check"></i> Priority customer support</li>
                    </ul>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="auth-form-panel">
                <h1>Create Account</h1>
                <p class="auth-subtitle">Fill in the details below to get started</p>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base; ?>/register.php" class="auth-form" id="registerForm" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="first_name" id="first_name" placeholder="John" value="<?php echo $old['first_name'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="last_name" id="last_name" placeholder="Doe" value="<?php echo $old['last_name'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" id="email" placeholder="you@example.com" value="<?php echo $old['email'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" name="phone" id="phone" placeholder="+1 234 567 8900" value="<?php echo $old['phone'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="password" placeholder="Min 8 characters" required minlength="8">
                                <button type="button" class="toggle-pass" data-target="password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter password" required minlength="8">
                                <button type="button" class="toggle-pass" data-target="confirm_password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" value="1" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="<?php echo $base; ?>/terms-conditions.php" target="_blank">Terms & Conditions</a> and <a href="<?php echo $base; ?>/privacy-policy.php" target="_blank">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-auth-submit">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>

                <div class="auth-footer-link">
                    Already have an account? <a href="<?php echo $base; ?>/login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
