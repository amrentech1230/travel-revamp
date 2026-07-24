<?php
/**
 * User Profile Page - TravenzoTravel
 */
$pageTitle = 'My Profile';
require_once 'includes/header.php';
$base = BASE_PATH;

requireLogin();

$db = getDB();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $errors[] = 'Invalid submission.';
    } else {
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName  = sanitize($_POST['last_name'] ?? '');
        $phone     = sanitize($_POST['phone'] ?? '');
        $dob       = sanitize($_POST['dob'] ?? '');
        $gender    = sanitize($_POST['gender'] ?? '');
        $address   = sanitize($_POST['address'] ?? '');
        $city      = sanitize($_POST['city'] ?? '');
        $state     = sanitize($_POST['state'] ?? '');
        $country   = sanitize($_POST['country'] ?? '');
        $zip       = sanitize($_POST['zip'] ?? '');

        if (empty($firstName)) $errors[] = 'First name is required.';
        if (empty($lastName)) $errors[] = 'Last name is required.';

        // Password change
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 8) $errors[] = 'Password must be at least 8 characters.';
            if ($newPassword !== $confirmPassword) $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            $sql = "UPDATE users SET first_name=?, last_name=?, phone=?, dob=?, gender=?, address=?, city=?, state=?, country=?, zip=?";
            $params = [$firstName, $lastName, $phone, $dob ?: null, $gender ?: null, $address, $city, $state, $country, $zip];

            if (!empty($newPassword)) {
                $sql .= ", password=?";
                $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $sql .= " WHERE id=?";
            $params[] = $_SESSION['user_id'];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            $_SESSION['user_name'] = $firstName . ' ' . $lastName;
            $success = true;

            // Refresh user data
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        }
    }
}
?>

<section class="page-banner">
    <div class="container">
        <h1>My <span>Profile</span></h1>
        <p>Manage your personal information and preferences</p>
    </div>
</section>

<section class="profile-section">
    <div class="container">
        <div class="profile-layout">
            <!-- Sidebar -->
            <aside class="profile-sidebar">
                <div class="profile-avatar">
                    <div class="avatar-circle">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                    <h3><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
                    <p><?php echo $user['email']; ?></p>
                </div>
                <nav class="profile-nav">
                    <a href="<?php echo $base; ?>/profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
                    <a href="<?php echo $base; ?>/my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                    <a href="<?php echo $base; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </aside>

            <!-- Form -->
            <div class="profile-main">
                <?php if ($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> Profile updated successfully!</div>
                <?php endif; ?>
                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base; ?>/profile.php" class="profile-form" novalidate>
                    <?php echo csrfField(); ?>

                    <h2>Personal Information</h2>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" value="<?php echo sanitize($user['first_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" value="<?php echo sanitize($user['last_name']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?php echo sanitize($user['email']); ?>" disabled>
                            <small class="form-hint">Email cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" value="<?php echo sanitize($user['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo $user['dob'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select name="gender">
                                <option value="">Select</option>
                                <option value="male" <?php echo ($user['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo ($user['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo ($user['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <h2>Address</h2>
                    <div class="form-group">
                        <label>Street Address</label>
                        <input type="text" name="address" value="<?php echo sanitize($user['address'] ?? ''); ?>">
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="<?php echo sanitize($user['city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="<?php echo sanitize($user['state'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="zip" value="<?php echo sanitize($user['zip'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" value="<?php echo sanitize($user['country'] ?? ''); ?>">
                    </div>

                    <h2>Change Password</h2>
                    <p class="form-hint">Leave blank if you don't want to change your password</p>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Min 8 characters" minlength="8">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" placeholder="Re-enter password">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
