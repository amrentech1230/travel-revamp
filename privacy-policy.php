<?php
/**
 * Privacy Policy Page - TravenzoTravel
 */
$pageTitle = 'Privacy Policy';
require_once 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Privacy <span>Policy</span></h1>
        <p>Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="policy-content">
            <h2>1. Introduction</h2>
            <p>TravenzoTravel ("we," "us," or "our") respects your privacy and is committed to protecting your personal data. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or make a booking.</p>

            <h2>2. Information We Collect</h2>
            <h3>Personal Information:</h3>
            <ul>
                <li>Full name, email address, phone number</li>
                <li>Date of birth, gender, nationality</li>
                <li>Passport details (for international bookings)</li>
                <li>Billing address and payment card details</li>
                <li>Travel preferences and booking history</li>
            </ul>
            <h3>Automatically Collected Information:</h3>
            <ul>
                <li>IP address, browser type, operating system</li>
                <li>Pages visited, time spent, referring URLs</li>
                <li>Device information and cookies</li>
            </ul>

            <h2>3. How We Use Your Information</h2>
            <ul>
                <li>Process and confirm flight bookings</li>
                <li>Process payments securely via Authorize.Net</li>
                <li>Send booking confirmations and e-tickets</li>
                <li>Provide customer support and resolve disputes</li>
                <li>Send promotional offers (with your consent)</li>
                <li>Improve our website and services</li>
                <li>Comply with legal obligations</li>
            </ul>

            <h2>4. Payment Security</h2>
            <p>All payment transactions are processed through Authorize.Net, a PCI-DSS Level 1 certified payment processor. We do not store your full credit card number on our servers. Payment data is encrypted using industry-standard SSL/TLS encryption.</p>

            <h2>5. Information Sharing</h2>
            <p>We may share your information with:</p>
            <ul>
                <li><strong>Airlines:</strong> To complete your flight booking and issue tickets</li>
                <li><strong>Payment Processors:</strong> Authorize.Net for secure transaction processing</li>
                <li><strong>Travel Technology Partners:</strong> Mondee for flight inventory and booking management</li>
                <li><strong>Legal Authorities:</strong> When required by law or to protect our rights</li>
            </ul>
            <p>We do NOT sell your personal data to third parties for marketing purposes.</p>

            <h2>6. Cookies</h2>
            <p>We use cookies to enhance your browsing experience, remember your preferences, and analyze website traffic. You can control cookies through your browser settings.</p>

            <h2>7. Data Retention</h2>
            <p>We retain your personal data for as long as necessary to fulfill the purposes outlined in this policy, typically up to 7 years for booking and financial records as required by law.</p>

            <h2>8. Your Rights</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access your personal data</li>
                <li>Correct inaccurate information</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of marketing communications</li>
                <li>Request data portability</li>
            </ul>

            <h2>9. Children's Privacy</h2>
            <p>Our services are not directed to individuals under 18. We do not knowingly collect personal information from children without parental consent.</p>

            <h2>10. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated effective date.</p>

            <h2>11. Contact Us</h2>
            <p>For privacy-related inquiries:</p>
            <ul>
                <li>Email: <?php echo SITE_EMAIL; ?></li>
                <li>Phone: <?php echo SITE_PHONE; ?></li>
                <li>Address: <?php echo SITE_ADDRESS; ?></li>
            </ul>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
