<?php
/**
 * About Us Page - TravenzoTravel
 */
$pageTitle = 'About Us';
require_once 'includes/header.php';
$base = BASE_PATH;
?>

<!-- Banner with Airport Background -->
<section class="page-banner banner-about">
    <div class="container">
        <h1 data-aos="fade-down">About <span>TravenzoTravel</span></h1>
        <p data-aos="fade-up">Your Trusted Partner for Air Travel Worldwide</p>
    </div>
</section>

<!-- About Intro Section - Airport/Travel Background -->
<section class="about-section about-bg-section">
    <div class="container">
        <div class="about-grid" data-aos="fade-up">
            <div class="about-content">
                <h2>Who We Are</h2>
                <p>TravenzoTravel is a leading online flight booking platform dedicated to providing travelers with the best airfares across 500+ airlines worldwide. Founded with a mission to make air travel accessible and affordable, we leverage cutting-edge technology powered by <strong>Mondee Travel Technology</strong>.</p>
                <p>We provide real-time flight inventory, instant booking confirmations, and seamless payment processing through <strong>Authorize.Net's</strong> secure gateway - ensuring your every transaction is safe and swift.</p>
                <p>Whether you're planning a domestic getaway or an international adventure, TravenzoTravel is your one-stop destination for hassle-free flight bookings at the lowest prices.</p>
                <div class="about-features-mini">
                    <div class="afm-item"><i class="fas fa-globe"></i> <span>50+ Countries</span></div>
                    <div class="afm-item"><i class="fas fa-plane"></i> <span>500+ Airlines</span></div>
                    <div class="afm-item"><i class="fas fa-headset"></i> <span>24/7 Support</span></div>
                </div>
            </div>
            <div class="about-image">
                <div class="about-img-main" style="background-image: url('https://images.unsplash.com/photo-1569154941061-e231b4725ef1?w=600&q=80')"></div>
                <div class="about-img-float" style="background-image: url('https://images.unsplash.com/photo-1556388158-158ea5ccacbd?w=300&q=80')"></div>
            </div>
        </div>
    </div>
</section>

<!-- Stats - Aerial City View Background -->
<section class="about-stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="stat-icon"><i class="fas fa-plane-departure"></i></div>
                <div class="stat-number">500+</div>
                <div class="stat-label">Airline Partners</div>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number">1M+</div>
                <div class="stat-label">Happy Travelers</div>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="stat-icon"><i class="fas fa-globe-americas"></i></div>
                <div class="stat-number">50+</div>
                <div class="stat-label">Countries Covered</div>
            </div>
            <div class="stat-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number">24/7</div>
                <div class="stat-label">Customer Support</div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section - World Map Background -->
<section class="about-mission about-mission-bg">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Our <span>Mission & Values</span></h2>
        <div class="mission-grid">
            <div class="mission-card" data-aos="fade-up" data-aos-delay="100">
                <div class="mission-icon"><i class="fas fa-bullseye"></i></div>
                <h3>Our Mission</h3>
                <p>To make air travel booking simple, affordable, and accessible for everyone. We strive to provide the lowest fares with transparent pricing.</p>
            </div>
            <div class="mission-card" data-aos="fade-up" data-aos-delay="200">
                <div class="mission-icon"><i class="fas fa-eye"></i></div>
                <h3>Our Vision</h3>
                <p>To become the most trusted flight booking platform globally, known for reliability, innovation, and customer satisfaction.</p>
            </div>
            <div class="mission-card" data-aos="fade-up" data-aos-delay="300">
                <div class="mission-icon"><i class="fas fa-heart"></i></div>
                <h3>Our Values</h3>
                <p>Transparency, customer-first approach, innovation, integrity, and commitment to delivering the best travel experience.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us - Airplane Interior Background -->
<section class="about-why about-why-bg">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Why Choose <span>Us</span></h2>
        <div class="why-grid">
            <div class="why-item" data-aos="fade-up" data-aos-delay="100">
                <div class="why-icon-circle"><i class="fas fa-tags"></i></div>
                <h4>Best Price Guarantee</h4>
                <p>We compare prices across hundreds of airlines to ensure you always get the lowest fare.</p>
            </div>
            <div class="why-item" data-aos="fade-up" data-aos-delay="200">
                <div class="why-icon-circle"><i class="fas fa-shield-alt"></i></div>
                <h4>Secure Payments</h4>
                <p>All transactions processed through Authorize.Net with bank-grade encryption.</p>
            </div>
            <div class="why-item" data-aos="fade-up" data-aos-delay="300">
                <div class="why-icon-circle"><i class="fas fa-headset"></i></div>
                <h4>24/7 Expert Support</h4>
                <p>Dedicated travel experts available round the clock via phone, email, and chat.</p>
            </div>
            <div class="why-item" data-aos="fade-up" data-aos-delay="400">
                <div class="why-icon-circle"><i class="fas fa-undo"></i></div>
                <h4>Easy Cancellation</h4>
                <p>Flexible cancellation policies with hassle-free refund processing.</p>
            </div>
            <div class="why-item" data-aos="fade-up" data-aos-delay="500">
                <div class="why-icon-circle"><i class="fas fa-bolt"></i></div>
                <h4>Instant Confirmation</h4>
                <p>Get your e-ticket and PNR immediately after booking. No waiting time.</p>
            </div>
            <div class="why-item" data-aos="fade-up" data-aos-delay="600">
                <div class="why-icon-circle"><i class="fas fa-globe"></i></div>
                <h4>Global Coverage</h4>
                <p>Book flights to 50+ countries with access to 500+ international airlines.</p>
            </div>
        </div>
    </div>
</section>

<!-- Partners - Runway Background -->
<section class="about-partners about-partners-bg">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Our <span>Technology Partners</span></h2>
        <p class="section-subtitle" data-aos="fade-up">Powered by industry-leading travel technology</p>
        <div class="partners-grid" data-aos="fade-up" data-aos-delay="200">
            <div class="partner-card">
                <i class="fas fa-server"></i>
                <h4>Mondee</h4>
                <p>Flight inventory & booking engine</p>
            </div>
            <div class="partner-card">
                <i class="fas fa-lock"></i>
                <h4>Authorize.Net</h4>
                <p>Secure payment processing</p>
            </div>
            <div class="partner-card">
                <i class="fas fa-plane"></i>
                <h4>500+ Airlines</h4>
                <p>Global airline partnerships</p>
            </div>
            <div class="partner-card">
                <i class="fas fa-shield-alt"></i>
                <h4>PCI-DSS</h4>
                <p>Level 1 security compliance</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
