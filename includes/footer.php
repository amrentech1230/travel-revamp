</main>
<!-- ═══ Main Content End ═══ -->
<?php $base = BASE_PATH; ?>

<!-- ═══ Newsletter Section ═══ -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-box" data-aos="fade-up">
            <div class="newsletter-text">
                <h3><i class="fas fa-paper-plane"></i> Get Exclusive Flight Deals</h3>
                <p>Subscribe to our newsletter and never miss a deal on cheap flights.</p>
            </div>
            <form class="newsletter-form" id="newsletterForm" method="POST" action="<?php echo $base; ?>/ajax/newsletter.php">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit"><i class="fas fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</section>

<!-- ═══ Footer ═══ -->
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Col 1: Brand -->
            <div class="footer-col">
                <div class="footer-brand">
                    <span class="logo-primary">Travenzo</span><span class="logo-accent">Travel</span>
                </div>
                <p>Your trusted partner for booking domestic & international flights at the best prices. 24/7 customer support and instant confirmations.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Col 2: Quick Links -->
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo $base; ?>/">Search Flights</a></li>
                    <li><a href="<?php echo $base; ?>/about.php">About Us</a></li>
                    <li><a href="<?php echo $base; ?>/contact.php">Contact Us</a></li>
                    <li><a href="<?php echo $base; ?>/login.php">My Account</a></li>
                </ul>
            </div>

            <!-- Col 3: Policies -->
            <div class="footer-col">
                <h4>Policies</h4>
                <ul>
                    <li><a href="<?php echo $base; ?>/refund-policy.php">Refund Policy</a></li>
                    <li><a href="<?php echo $base; ?>/privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="<?php echo $base; ?>/terms-conditions.php">Terms & Conditions</a></li>
                    <li><a href="<?php echo $base; ?>/disclaimer.php">Disclaimer</a></li>
                </ul>
            </div>

            <!-- Col 4: Contact -->
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul class="footer-contact-list">
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo SITE_ADDRESS; ?></li>
                    <li><i class="fas fa-phone-alt"></i> <?php echo SITE_PHONE; ?></li>
                    <li><a href="mailto:<?php echo SITE_EMAIL; ?>"><i class="fas fa-envelope"></i> <?php echo SITE_EMAIL; ?></a></li>
                    <li><i class="fas fa-clock"></i> 24/7 Customer Support</li>
                </ul>
            </div>
        </div>

        <!-- Payment Icons -->
        <div class="footer-payments">
            <span>We Accept:</span>
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fab fa-cc-amex"></i>
            <i class="fab fa-cc-discover"></i>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights Reserved.</p>
            <p class="footer-powered">Powered by Mondee Travel Technology | Secured by Authorize.Net</p>
        </div>
    </div>
</footer>

<!-- Back To Top -->
<button class="back-to-top" id="backToTop"><i class="fas fa-chevron-up"></i></button>

<!-- AOS Animation Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>AOS.init({ duration: 800, once: true, offset: 100 });</script>

<!-- JavaScript -->
<script src="<?php echo $base; ?>/assets/js/main.js"></script>
</body>
</html>
