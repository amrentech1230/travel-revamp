<?php
/**
 * Disclaimer Page - TravenzoTravel
 */
$pageTitle = 'Disclaimer';
require_once 'includes/header.php';
?>

<section class="page-banner">
    <div class="container">
        <h1><span>Disclaimer</span></h1>
        <p>Last updated: <?php echo date('F d, Y'); ?></p>
    </div>
</section>

<section class="policy-section">
    <div class="container">
        <div class="policy-content">
            <h2>1. General Disclaimer</h2>
            <p>The information provided on TravenzoTravel (travenzotravel.com) is for general informational purposes only. While we strive to keep information accurate and up-to-date, we make no representations or warranties of any kind about the completeness, accuracy, reliability, or availability of the website or the information, products, services, or related graphics contained on the website.</p>

            <h2>2. Not an Airline</h2>
            <p>TravenzoTravel is an online travel agency and booking platform. We are NOT an airline. We act as an intermediary between you and the airlines. All flights are operated by their respective airlines, and we have no control over their operations, schedules, or policies.</p>

            <h2>3. Pricing Disclaimer</h2>
            <ul>
                <li>All prices displayed are subject to change without notice until booking is confirmed.</li>
                <li>Prices are sourced from our travel technology partners and may vary in real-time.</li>
                <li>Displayed prices may not include all taxes and fees until the final checkout step.</li>
                <li>Promotional prices are subject to availability and specific terms.</li>
            </ul>

            <h2>4. Third-Party Services</h2>
            <p>Our platform utilizes third-party services including but not limited to:</p>
            <ul>
                <li><strong>Mondee:</strong> For flight inventory, search, and booking technology</li>
                <li><strong>Authorize.Net:</strong> For secure payment processing</li>
                <li><strong>Airlines:</strong> For actual flight operations and ticketing</li>
            </ul>
            <p>We are not responsible for the actions, policies, or failures of these third-party services.</p>

            <h2>5. No Guarantee of Availability</h2>
            <p>Flight availability shown on our platform is subject to real-time changes. A fare displayed during search may not be available at the time of booking. We do not guarantee the availability of any flight or fare.</p>

            <h2>6. Travel Advisory</h2>
            <p>It is the traveler's responsibility to check and comply with all travel requirements including visas, health regulations, COVID-19 protocols, and entry restrictions for their destination. TravenzoTravel is not responsible for denied boarding due to non-compliance with travel regulations.</p>

            <h2>7. Website Availability</h2>
            <p>We do not guarantee that our website will be available at all times. We may experience hardware, software, or other problems, resulting in interruptions, delays, or errors. We reserve the right to change, revise, update, suspend, discontinue, or modify the website at any time.</p>

            <h2>8. Limitation of Liability</h2>
            <p>In no event shall TravenzoTravel, its directors, employees, partners, agents, or affiliates be liable for any indirect, incidental, special, consequential, or punitive damages arising out of your use of, or inability to use, our services.</p>

            <h2>9. External Links</h2>
            <p>Our website may contain links to third-party websites. These links are provided for convenience only. We do not endorse or assume responsibility for the content or practices of any linked third-party sites.</p>

            <h2>10. Contact</h2>
            <p>If you have questions about this disclaimer, contact us at:</p>
            <ul>
                <li>Email: <?php echo SITE_EMAIL; ?></li>
                <li>Phone: <?php echo SITE_PHONE; ?></li>
            </ul>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
