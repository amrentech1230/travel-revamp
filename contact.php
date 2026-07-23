<?php
/**
 * Contact Us Page - TravenzoTravel
 */
$pageTitle = 'Contact Us';
require_once 'includes/header.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $errors[] = 'Invalid form submission.';
    } else {
        $name    = sanitize($_POST['name'] ?? '');
        $email   = sanitize($_POST['email'] ?? '');
        $phone   = sanitize($_POST['phone'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');
        $bookRef = sanitize($_POST['booking_ref'] ?? '');

        if (empty($name)) $errors[] = 'Name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (empty($subject)) $errors[] = 'Subject is required.';
        if (empty($message)) $errors[] = 'Message is required.';

        if (empty($errors)) {
            try {
                $db = getDB();
                $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message, booking_ref, ip_address) VALUES (?,?,?,?,?,?,?)");
                $stmt->execute([$name, $email, $phone, $subject, $message, $bookRef, $_SERVER['REMOTE_ADDR'] ?? '']);
                $success = true;
            } catch (Exception $e) {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<section class="page-banner">
    <div class="container">
        <h1>Contact <span>Us</span></h1>
        <p>We're here to help 24/7. Reach out to us anytime.</p>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info Cards -->
            <div class="contact-info">
                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-phone-alt"></i></div>
                    <h3>Call Us</h3>
                    <p><?php echo SITE_PHONE; ?></p>
                    <small>Available 24/7</small>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-envelope"></i></div>
                    <h3>Email Us</h3>
                    <p><?php echo SITE_EMAIL; ?></p>
                    <small>Response within 24 hours</small>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <h3>Visit Us</h3>
                    <p><?php echo SITE_ADDRESS; ?></p>
                    <small>Mon-Fri: 9AM - 6PM</small>
                </div>
                <div class="contact-card">
                    <div class="contact-card-icon"><i class="fas fa-comments"></i></div>
                    <h3>Live Chat</h3>
                    <p>Chat with our agents</p>
                    <small>Available 24/7</small>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <h2>Send Us a Message</h2>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Thank you! Your message has been sent. We'll get back to you within 24 hours.
                </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="/contact.php" class="contact-form" novalidate>
                    <?php echo csrfField(); ?>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Your Name *</label>
                            <input type="text" name="name" placeholder="Full name" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" placeholder="you@email.com" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="phone" placeholder="Phone number">
                        </div>
                        <div class="form-group">
                            <label>Booking Reference</label>
                            <input type="text" name="booking_ref" placeholder="e.g. TRV12345678">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Subject *</label>
                        <select name="subject" required>
                            <option value="">Select a topic</option>
                            <option value="Booking Inquiry">Booking Inquiry</option>
                            <option value="Cancellation Request">Cancellation Request</option>
                            <option value="Refund Status">Refund Status</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Flight Change">Flight Change</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Complaint">Complaint</option>
                            <option value="Feedback">Feedback</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" rows="5" placeholder="Describe your query..." required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
