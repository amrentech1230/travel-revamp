<?php
/**
 * Homepage - TravenzoTravel
 * MakeMyTrip-style flight search with hero, offers, features
 */
$pageTitle = 'Book Cheap Flights Online';
require_once 'includes/header.php';
$base = BASE_PATH;
?>

<!-- ═══ Hero Section with Flight Search ═══ -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-particles"></div>
    <div class="container">
        <div class="hero-content" data-aos="fade-down">
            <h1>Find & Book <span>Cheap Flights</span></h1>
            <p>Search 500+ airlines. Best prices guaranteed. Instant booking confirmation.</p>
        </div>

        <!-- Flight Search Box -->
        <div class="search-box" data-aos="fade-up" data-aos-delay="200">
            <div class="search-tabs">
                <button class="search-tab active"><i class="fas fa-plane"></i> Flights</button>
            </div>

            <form action="<?php echo $base; ?>/search-results.php" method="GET" id="flightSearchForm" class="flight-search-form">
                <!-- Trip Type -->
                <div class="trip-type-row">
                    <label class="trip-radio">
                        <input type="radio" name="trip_type" value="oneway" checked>
                        <span class="radio-pill">One Way</span>
                    </label>
                    <label class="trip-radio">
                        <input type="radio" name="trip_type" value="roundtrip">
                        <span class="radio-pill">Round Trip</span>
                    </label>
                    <label class="trip-radio">
                        <input type="radio" name="trip_type" value="multicity">
                        <span class="radio-pill">Multi City</span>
                    </label>
                </div>

                <!-- Search Fields Row -->
                <div class="search-fields">
                    <div class="field-group field-from">
                        <label>FROM</label>
                        <input type="text" name="origin" id="originInput" placeholder="Enter city or airport" autocomplete="off" required>
                        <input type="hidden" name="origin_code" id="originCode">
                        <div class="airport-suggestions" id="originSuggestions"></div>
                    </div>
                    <button type="button" class="swap-btn" id="swapBtn" title="Swap cities">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    <div class="field-group field-to">
                        <label>TO</label>
                        <input type="text" name="destination" id="destInput" placeholder="Enter city or airport" autocomplete="off" required>
                        <input type="hidden" name="destination_code" id="destCode">
                        <div class="airport-suggestions" id="destSuggestions"></div>
                    </div>
                    <div class="field-group field-date">
                        <label>DEPARTURE</label>
                        <input type="date" name="departure_date" id="depDate" required min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="field-group field-date field-return" id="returnField" style="display:none;">
                        <label>RETURN</label>
                        <input type="date" name="return_date" id="retDate" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="field-group field-travelers">
                        <label>TRAVELERS & CLASS</label>
                        <div class="travelers-trigger" id="travelersTrigger">
                            <span id="travelersText">1 Adult, Economy</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="travelers-dropdown" id="travelersDropdown">
                            <div class="pax-row">
                                <div class="pax-label"><strong>Adults</strong><small>12+ yrs</small></div>
                                <div class="pax-counter">
                                    <button type="button" class="pax-btn minus" data-target="adults">−</button>
                                    <input type="number" name="adults" id="adults" value="1" min="1" max="9" readonly>
                                    <button type="button" class="pax-btn plus" data-target="adults">+</button>
                                </div>
                            </div>
                            <div class="pax-row">
                                <div class="pax-label"><strong>Children</strong><small>2-11 yrs</small></div>
                                <div class="pax-counter">
                                    <button type="button" class="pax-btn minus" data-target="children">−</button>
                                    <input type="number" name="children" id="children" value="0" min="0" max="9" readonly>
                                    <button type="button" class="pax-btn plus" data-target="children">+</button>
                                </div>
                            </div>
                            <div class="pax-row">
                                <div class="pax-label"><strong>Infants</strong><small>Under 2 yrs</small></div>
                                <div class="pax-counter">
                                    <button type="button" class="pax-btn minus" data-target="infants">−</button>
                                    <input type="number" name="infants" id="infants" value="0" min="0" max="4" readonly>
                                    <button type="button" class="pax-btn plus" data-target="infants">+</button>
                                </div>
                            </div>
                            <div class="cabin-select">
                                <label>Cabin Class</label>
                                <select name="cabin_class" id="cabinClass">
                                    <option value="economy">Economy</option>
                                    <option value="premium_economy">Premium Economy</option>
                                    <option value="business">Business</option>
                                    <option value="first">First Class</option>
                                </select>
                            </div>
                            <button type="button" class="btn-apply-pax" id="applyPax">Done</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-search-flights">
                    <i class="fas fa-search"></i> SEARCH FLIGHTS
                </button>
            </form>
        </div>
    </div>
</section>

<!-- ═══ Features Section ═══ -->
<section class="section-features">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Why Choose <span>TravenzoTravel</span></h2>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon"><i class="fas fa-dollar-sign"></i></div>
                <h3>Lowest Prices</h3>
                <p>Compare 500+ airlines & get the lowest fares. Price match guarantee on every booking.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure Payments</h3>
                <p>Pay securely via Authorize.Net with PCI-DSS compliant processing & fraud protection.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Dedicated travel experts available round the clock via phone, chat & email.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Instant Confirmation</h3>
                <p>Get e-ticket & PNR instantly after booking. No waiting, no hassle.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══ Popular Routes with Travel Images ═══ -->
<section class="section-popular">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Popular <span>Flight Destinations</span></h2>
        <p class="section-subtitle" data-aos="fade-up">Explore the world with unbeatable fares</p>
        <div class="routes-grid">
            <?php
            $routes = [
                ['from' => 'DEL', 'to' => 'BOM', 'fromCity' => 'New Delhi', 'toCity' => 'Mumbai', 'price' => 89, 'tag' => 'Popular', 'img' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=400&h=250&fit=crop'],
                ['from' => 'DEL', 'to' => 'BLR', 'fromCity' => 'New Delhi', 'toCity' => 'Bangalore', 'price' => 109, 'tag' => 'Trending', 'img' => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=400&h=250&fit=crop'],
                ['from' => 'BOM', 'to' => 'GOI', 'fromCity' => 'Mumbai', 'toCity' => 'Goa', 'price' => 65, 'tag' => 'Hot Deal', 'img' => 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=400&h=250&fit=crop'],
                ['from' => 'JFK', 'to' => 'LHR', 'fromCity' => 'New York', 'toCity' => 'London', 'price' => 399, 'tag' => 'International', 'img' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=400&h=250&fit=crop'],
                ['from' => 'LAX', 'to' => 'SIN', 'fromCity' => 'Los Angeles', 'toCity' => 'Singapore', 'price' => 499, 'tag' => 'Best Seller', 'img' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=400&h=250&fit=crop'],
                ['from' => 'DEL', 'to' => 'DXB', 'fromCity' => 'New Delhi', 'toCity' => 'Dubai', 'price' => 249, 'tag' => 'Offer', 'img' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=400&h=250&fit=crop'],
            ];
            foreach ($routes as $idx => $route):
                $searchLink = $base . '/search-results.php?origin_code=' . $route['from'] . '&destination_code=' . $route['to'] . '&departure_date=' . date('Y-m-d', strtotime('+7 days')) . '&adults=1&cabin_class=economy&trip_type=oneway';
            ?>
            <div class="route-card" data-aos="fade-up" data-aos-delay="<?php echo ($idx + 1) * 100; ?>">
                <div class="route-image" style="background-image: url('<?php echo $route['img']; ?>')">
                    <div class="route-image-overlay"></div>
                    <span class="route-tag"><?php echo $route['tag']; ?></span>
                </div>
                <div class="route-body">
                    <div class="route-cities">
                        <span class="route-from"><?php echo $route['fromCity']; ?></span>
                        <i class="fas fa-plane"></i>
                        <span class="route-to"><?php echo $route['toCity']; ?></span>
                    </div>
                    <div class="route-codes"><?php echo $route['from']; ?> → <?php echo $route['to']; ?></div>
                    <div class="route-price">
                        <small>Starting from</small>
                        <strong><?php echo formatPrice($route['price']); ?></strong>
                    </div>
                    <a href="<?php echo $searchLink; ?>" class="btn-route-book">Search Flights</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══ Travel CTA Banner with Background Image ═══ -->
<section class="travel-cta-banner" data-aos="fade-up">
    <div class="cta-overlay"></div>
    <div class="container">
        <div class="cta-content">
            <h2>Ready for Your Next Adventure?</h2>
            <p>Explore 50+ countries, 500+ airlines. Your dream destination awaits.</p>
            <a href="<?php echo $base; ?>/#flightSearchForm" class="btn-cta-search"><i class="fas fa-search"></i> Search Flights Now</a>
        </div>
    </div>
</section>

<!-- ═══ Offers Section ═══ -->
<section class="section-offers">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Exclusive <span>Offers</span></h2>
        <div class="offers-grid">
            <div class="offer-card offer-big" data-aos="fade-right">
                <div class="offer-badge">FLAT 15% OFF</div>
                <h3>First Flight Booking</h3>
                <p>Use code <strong>FIRST15</strong> and get flat 15% discount on your first booking.</p>
                <span class="offer-validity">Valid till <?php echo date('M d, Y', strtotime('+30 days')); ?></span>
            </div>
            <div class="offer-card" data-aos="fade-up" data-aos-delay="100">
                <div class="offer-badge">UP TO $50 OFF</div>
                <h3>Domestic Flights</h3>
                <p>Save up to $50 with code <strong>DOMFLY50</strong></p>
                <span class="offer-validity">Limited Time</span>
            </div>
            <div class="offer-card" data-aos="fade-left" data-aos-delay="200">
                <div class="offer-badge">$100 OFF</div>
                <h3>International Flights</h3>
                <p>Flat $100 off on round-trip with code <strong>INTL100</strong></p>
                <span class="offer-validity">Valid on round trips</span>
            </div>
        </div>
    </div>
</section>

<!-- ═══ How It Works ═══ -->
<section class="section-steps">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">How It <span>Works</span></h2>
        <div class="steps-grid">
            <div class="step-card" data-aos="zoom-in" data-aos-delay="100">
                <div class="step-num">1</div>
                <div class="step-icon"><i class="fas fa-search"></i></div>
                <h3>Search</h3>
                <p>Enter travel details to find flights across 500+ airlines.</p>
            </div>
            <div class="step-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="step-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="step-num">2</div>
                <div class="step-icon"><i class="fas fa-filter"></i></div>
                <h3>Compare</h3>
                <p>Filter by price, stops, airlines & choose the best option.</p>
            </div>
            <div class="step-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="step-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="step-num">3</div>
                <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                <h3>Book & Pay</h3>
                <p>Enter passenger info & pay securely via Authorize.Net.</p>
            </div>
            <div class="step-arrow"><i class="fas fa-arrow-right"></i></div>
            <div class="step-card" data-aos="zoom-in" data-aos-delay="400">
                <div class="step-num">4</div>
                <div class="step-icon"><i class="fas fa-ticket-alt"></i></div>
                <h3>Get E-Ticket</h3>
                <p>Receive instant confirmation & e-ticket on your email.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══ Testimonials ═══ -->
<section class="section-testimonials">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">What Travelers <span>Say</span></h2>
        <div class="testimonials-grid">
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p>"Booked my family flights to London. Got the cheapest fare compared to any other site. Super smooth!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">RK</div>
                    <div><strong>Rahul Kumar</strong><br><small>Delhi, India</small></div>
                </div>
            </div>
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p>"Excellent customer support! Had an issue with my booking and they resolved it within minutes."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">SP</div>
                    <div><strong>Sarah Parker</strong><br><small>New York, USA</small></div>
                </div>
            </div>
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                </div>
                <p>"Easy refund process and transparent policies. TravenzoTravel is my go-to for all flight bookings."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">AM</div>
                    <div><strong>Amit Mehta</strong><br><small>Mumbai, India</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
