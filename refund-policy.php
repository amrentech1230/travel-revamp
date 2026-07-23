<?php
/**
 * Refund Policy Page - TravenzoTravel
 */
$pageTitle = 'Refund Policy';
require_once 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1>Refund <span>Policy</span></h1>
        <p>Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="policy-content">
            <h2>1. Overview</h2>
            <p>At TravenzoTravel, we understand that travel plans can change. This Refund Policy outlines the terms and conditions under which refunds are processed for flight bookings made through our platform.</p>

            <h2>2. Cancellation & Refund Eligibility</h2>
            <ul>
                <li><strong>Refundable Tickets:</strong> If you purchased a refundable ticket, you are eligible for a full or partial refund as per the airline's fare rules.</li>
                <li><strong>Non-Refundable Tickets:</strong> Non-refundable tickets are generally not eligible for a refund. However, taxes and fees may be refundable depending on the airline.</li>
                <li><strong>24-Hour Free Cancellation:</strong> Bookings cancelled within 24 hours of purchase (and at least 7 days before departure) are eligible for a full refund regardless of fare type.</li>
            </ul>

            <h2>3. How to Request a Refund</h2>
            <p>To request a refund, you can:</p>
            <ul>
                <li>Login to your account and navigate to "My Bookings" to initiate cancellation</li>
                <li>Contact our support team at <strong><?php echo SITE_PHONE; ?></strong></li>
                <li>Email us at <strong><?php echo SITE_EMAIL; ?></strong> with your booking reference</li>
                <li>Use the <a href="/contact.php">Contact Us</a> form with subject "Cancellation Request"</li>
            </ul>

            <h2>4. Refund Processing Time</h2>
            <table class="policy-table">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Processing Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Credit Card</td><td>7-10 business days</td></tr>
                    <tr><td>Debit Card</td><td>7-14 business days</td></tr>
                    <tr><td>Net Banking</td><td>5-7 business days</td></tr>
                    <tr><td>UPI / Wallet</td><td>3-5 business days</td></tr>
                </tbody>
            </table>

            <h2>5. Cancellation Charges</h2>
            <p>The following charges may apply when you cancel a booking:</p>
            <ul>
                <li><strong>TravenzoTravel Service Fee:</strong> A non-refundable service fee of $25 per passenger per segment may apply.</li>
                <li><strong>Airline Cancellation Fee:</strong> Airlines charge their own cancellation penalty which varies by carrier, route, and fare class.</li>
                <li><strong>No-Show:</strong> If you miss your flight without cancelling, no refund will be provided.</li>
            </ul>

            <h2>6. Partial Refunds</h2>
            <p>In cases where only a portion of your booking is cancelled (e.g., one leg of a round trip), a partial refund will be calculated based on the individual segment fare and applicable penalties.</p>

            <h2>7. Airline-Initiated Cancellations</h2>
            <p>If the airline cancels your flight, you are entitled to a full refund including all fees. TravenzoTravel will process this automatically and notify you via email.</p>

            <h2>8. Refund for Failed Transactions</h2>
            <p>If your payment was deducted but the booking was not confirmed, the amount will be automatically refunded within 5-7 business days. If not received, please contact our support team.</p>

            <h2>9. Contact Us</h2>
            <p>For any refund-related queries, please reach out to:</p>
            <ul>
                <li>Phone: <?php echo SITE_PHONE; ?> (24/7)</li>
                <li>Email: <?php echo SITE_EMAIL; ?></li>
                <li>Address: <?php echo SITE_ADDRESS; ?></li>
            </ul>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
