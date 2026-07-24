<?php
/**
 * Contact Us Page - TravenzoTravel
 */
$pageTitle = 'Contact Us';
require_once 'includes/header.php';
$base = BASE_PATH;

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

<!-- Banner - Airport Check-in Counter -->
<section class="page-banner banner-contact">
    <div class="container">
        <h1 data-aos="fade-down">Contact <span>Us</span></h1>
        <p data-aos="fade-up">We're here to help 24/7. Reach out to us anytime.</p>
    </div>
</section>

<!-- Contact Info Cards - Airplane Wing Background -->
<section class="contact-cards-section">
    <div class="container">
        <div class="contact-info-grid" data-aos="fade-up">
            <div class="contact-info-card">
                <div class="cic-icon"><i class="fas fa-phone-alt"></i></div>
                <h3>Call Us</h3>
                <p class="cic-main"><?php echo SITE_PHONE; ?></p>
                <span class="cic-sub">Available 24/7 - Toll Free</span>
            </div>
            <div class="contact-info-card">
                <div class="cic-icon"><i class="fas fa-envelope"></i></div>
                <h3>Email Us</h3>
                <p class="cic-main"><?php echo SITE_EMAIL; ?></p>
                <span class="cic-sub">Response within 2 hours</span>
            </div>
            <div class="contact-info-card">
                <div class="cic-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Visit Us</h3>
                <p class="cic-main"><?php echo SITE_ADDRESS; ?></p>
                <span class="cic-sub">Mon-Fri: 9AM - 6PM</span>
            </div>
            <div class="contact-info-card">
                <div class="cic-icon"><i class="fas fa-comments"></i></div>
                <h3>Live Chat</h3>
                <p class="cic-main">Chat with our agents</p>
                <span class="cic-sub">Instant response 24/7</span>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section - Travel Map Background -->
<section class="contact-form-section">
    <div class="container">
        <div class="contact-form-grid">
            <!-- Left Image Panel -->
            <div class="contact-left-panel" data-aos="fade-right">
                <div class="clp-image" style="background-image: url('https://images.unsplash.com/photo-1521295121783-8a321d551ad2?w=600&q=80')"></div>
                <div class="clp-overlay">
                    <h3><i class="fas fa-headset"></i> Get in Touch</h3>
                    <p>Our team of travel experts is ready to assist you with any query - booking, cancellation, refund, or general inquiry.</p>
                    <ul class="clp-list">
                        <li><i class="fas fa-check-circle"></i> Average response time: 2 hours</li>
                        <li><i class="fas fa-check-circle"></i> Multilingual support available</li>
                        <li><i class="fas fa-check-circle"></i> Dedicated booking support</li>
                        <li><i class="fas fa-check-circle"></i> Refund tracking assistance</li>
                    </ul>
                </div>
            </div>

            <!-- Right Form -->
            <div class="contact-form-wrapper" data-aos="fade-left">
                <h2>Send Us a Message</h2>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    Thank you! Your message has been sent. We'll get back to you within 2 hours.
                </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base; ?>/contact.php" class="contact-form" novalidate>
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
                        <textarea name="message" rows="5" placeholder="Describe your query in detail..." required></textarea>
                    </div>

                    <button type="submit" class="btn-primary btn-contact-submit">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map/Location Section - City Skyline Background -->
<section class="contact-map-section">
    <div class="container">
        <div class="map-placeholder" data-aos="fade-up">
            <div class="map-content">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Our Office</h3>
                <p><?php echo SITE_ADDRESS; ?></p>
                <a href="https://maps.google.com" target="_blank" class="btn-primary"><i class="fas fa-directions"></i> Get Directions</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
