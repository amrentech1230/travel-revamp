<?php
/**
 * Terms & Conditions Page - TravenzoTravel
 */
$pageTitle = 'Terms & Conditions';
require_once 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Terms & <span>Conditions</span></h1>
        <p>Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="policy-content">
            <h2>1. Acceptance of Terms</h2>
            <p>By accessing and using TravenzoTravel (travenzotravel.com), you accept and agree to be bound by these Terms & Conditions. If you do not agree, please do not use our services.</p>

            <h2>2. Services</h2>
            <p>TravenzoTravel provides an online platform for searching, comparing, and booking flight tickets. We act as an intermediary between you and the airlines. The actual air transportation is provided by the respective airlines.</p>

            <h2>3. User Account</h2>
            <ul>
                <li>You must provide accurate and complete information during registration.</li>
                <li>You are responsible for maintaining the confidentiality of your account credentials.</li>
                <li>You must be at least 18 years old to create an account.</li>
                <li>We reserve the right to suspend or terminate accounts that violate these terms.</li>
            </ul>

            <h2>4. Booking & Payment</h2>
            <ul>
                <li>All prices displayed are in USD unless otherwise stated.</li>
                <li>Prices are subject to change until payment is confirmed.</li>
                <li>Payments are processed securely via Authorize.Net.</li>
                <li>A booking is confirmed only after successful payment and receipt of confirmation email.</li>
                <li>You are responsible for verifying all booking details before payment.</li>
            </ul>

            <h2>5. Cancellation & Refunds</h2>
            <p>Cancellation and refund policies are governed by our <a href="/refund-policy.php">Refund Policy</a>. Airline-specific cancellation rules also apply.</p>

            <h2>6. Passenger Responsibility</h2>
            <ul>
                <li>Ensure all passenger names match government-issued IDs exactly.</li>
                <li>Arrive at the airport with sufficient time before departure.</li>
                <li>Carry valid travel documents (passport, visa) for international travel.</li>
                <li>Comply with airline baggage policies and restrictions.</li>
            </ul>

            <h2>7. Flight Changes by Airlines</h2>
            <p>Airlines may change flight schedules, cancel flights, or modify routes at their discretion. TravenzoTravel is not liable for airline-initiated changes but will assist you in rebooking or obtaining refunds.</p>

            <h2>8. Limitation of Liability</h2>
            <ul>
                <li>TravenzoTravel acts as a booking agent and is not the airline carrier.</li>
                <li>We are not liable for flight delays, cancellations, or baggage issues caused by airlines.</li>
                <li>Our total liability shall not exceed the booking amount paid.</li>
                <li>We are not liable for indirect, incidental, or consequential damages.</li>
            </ul>

            <h2>9. Intellectual Property</h2>
            <p>All content on this website including text, graphics, logos, and software is the property of TravenzoTravel and protected by copyright laws. Unauthorized use is prohibited.</p>

            <h2>10. Prohibited Activities</h2>
            <ul>
                <li>Using the website for fraudulent purposes</li>
                <li>Attempting to gain unauthorized access to our systems</li>
                <li>Scraping or copying website content without permission</li>
                <li>Submitting false information or fake bookings</li>
            </ul>

            <h2>11. Third-Party Links</h2>
            <p>Our website may contain links to third-party websites. We are not responsible for the content or practices of these external sites.</p>

            <h2>12. Governing Law</h2>
            <p>These Terms & Conditions are governed by the laws of the State of New York, United States. Any disputes shall be resolved in the courts of New York.</p>

            <h2>13. Contact</h2>
            <p>For questions about these terms, contact us at:</p>
            <ul>
                <li>Email: <?php echo SITE_EMAIL; ?></li>
                <li>Phone: <?php echo SITE_PHONE; ?></li>
            </ul>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
