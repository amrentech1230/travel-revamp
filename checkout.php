<?php
/**
 * Checkout Page - TravenzoTravel
 * Passenger details + Authorize.Net payment
 */
$pageTitle = 'Complete Your Booking';
require_once 'includes/header.php';
$base = BASE_PATH;

// Must be logged in
requireLogin();

// Get selected flight
$flightIdx = (int)($_GET['flight'] ?? -1);
$flights = $_SESSION['search_results'] ?? [];
$searchParams = $_SESSION['search_params'] ?? [];

if ($flightIdx < 0 || !isset($flights[$flightIdx])) {
    setFlash('error', 'Invalid flight selection. Please search again.');
    redirect('/');
}

$flight = $flights[$flightIdx];
$adults = (int)($searchParams['adults'] ?? 1);
$children = (int)($searchParams['children'] ?? 0);
$infants = (int)($searchParams['infants'] ?? 0);
$totalPax = $adults + $children + $infants;
$totalAmount = $flight['total_price'] * ($adults + $children) + ($flight['total_price'] * 0.1 * $infants);

$errors = [];
$bookingSuccess = false;


// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['_token'] ?? '')) {
        $errors[] = 'Invalid submission.';
    } else {
        // Collect passenger data
        $passengers = [];
        for ($i = 0; $i < $totalPax; $i++) {
            $passengers[] = [
                'type'       => $_POST["pax_type_$i"] ?? 'adult',
                'title'      => sanitize($_POST["pax_title_$i"] ?? ''),
                'first_name' => sanitize($_POST["pax_fname_$i"] ?? ''),
                'last_name'  => sanitize($_POST["pax_lname_$i"] ?? ''),
                'dob'        => sanitize($_POST["pax_dob_$i"] ?? ''),
                'gender'     => sanitize($_POST["pax_gender_$i"] ?? ''),
                'passport_no'=> sanitize($_POST["pax_passport_$i"] ?? ''),
                'nationality'=> sanitize($_POST["pax_nationality_$i"] ?? ''),
            ];
            if (empty($passengers[$i]['first_name']) || empty($passengers[$i]['last_name'])) {
                $errors[] = 'Passenger ' . ($i + 1) . ': Name is required.';
            }
        }

        // Card data
        $cardData = [
            'number' => $_POST['card_number'] ?? '',
            'expiry' => $_POST['card_expiry'] ?? '',
            'cvv'    => $_POST['card_cvv'] ?? '',
        ];
        $billing = [
            'first_name' => sanitize($_POST['billing_fname'] ?? ''),
            'last_name'  => sanitize($_POST['billing_lname'] ?? ''),
            'address'    => sanitize($_POST['billing_address'] ?? ''),
            'city'       => sanitize($_POST['billing_city'] ?? ''),
            'state'      => sanitize($_POST['billing_state'] ?? ''),
            'zip'        => sanitize($_POST['billing_zip'] ?? ''),
            'country'    => sanitize($_POST['billing_country'] ?? 'US'),
        ];

        if (empty($cardData['number']) || empty($cardData['expiry']) || empty($cardData['cvv'])) {
            $errors[] = 'Complete card details are required.';
        }


        if (empty($errors)) {
            $db = getDB();
            $bookingRef = generateRef('TRV');

            // Process payment via Authorize.Net
            $gateway = new AuthorizeNet();
            $payResult = $gateway->charge($cardData, $totalAmount, $billing, [
                'invoice'     => $bookingRef,
                'description' => "Flight {$flight['flight_number']} - {$searchParams['origin']} to {$searchParams['destination']}",
            ]);

            if ($payResult['success']) {
                // Book via Mondee API
                $mondee = new MondeeAPI();
                $contact = ['email' => $_SESSION['user_email'], 'phone' => $billing['first_name']];
                $mondeeResult = $mondee->createBooking($flight, $passengers, $contact);

                // Save booking to DB
                $stmt = $db->prepare("INSERT INTO bookings (booking_ref, user_id, mondee_pnr, trip_type, origin_code, origin_city, destination_code, destination_city, departure_date, return_date, cabin_class, airline_name, airline_code, flight_number, departure_time, arrival_time, duration, stops, adults, children, infants, base_fare, taxes, service_fee, total_amount, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute([
                    $bookingRef, $_SESSION['user_id'],
                    $mondeeResult['pnr'] ?? null, $searchParams['trip_type'],
                    $searchParams['origin'], $searchParams['origin'],
                    $searchParams['destination'], $searchParams['destination'],
                    $searchParams['departure_date'], $searchParams['return_date'] ?? null,
                    $searchParams['cabin_class'], $flight['airline_name'], $flight['airline_code'],
                    $flight['flight_number'], $flight['departure_time'], $flight['arrival_time'],
                    $flight['duration'], $flight['stops'],
                    $adults, $children, $infants,
                    $flight['base_fare'], $flight['taxes'], $flight['service_fee'], $totalAmount,
                    'confirmed'
                ]);
                $bookingId = $db->lastInsertId();

                // Save passengers
                foreach ($passengers as $pax) {
                    $stmt = $db->prepare("INSERT INTO passengers (booking_id, type, title, first_name, last_name, dob, gender, passport_no, nationality) VALUES (?,?,?,?,?,?,?,?,?)");
                    $stmt->execute([$bookingId, $pax['type'], $pax['title'], $pax['first_name'], $pax['last_name'], $pax['dob'] ?: null, $pax['gender'], $pax['passport_no'] ?: null, $pax['nationality'] ?: null]);
                }

                // Save payment
                $transId = generateRef('TXN');
                $stmt = $db->prepare("INSERT INTO payments (booking_id, user_id, transaction_id, authnet_trans_id, amount, payment_method, card_last_four, card_type, status, response_code) VALUES (?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$bookingId, $_SESSION['user_id'], $transId, $payResult['transaction_id'], $totalAmount, 'credit_card', $payResult['card_last_four'] ?? '', $payResult['card_type'] ?? '', 'success', $payResult['response_code'] ?? '1']);

                $bookingSuccess = true;
                $_SESSION['last_booking_ref'] = $bookingRef;
            } else {
                $errors[] = 'Payment failed: ' . ($payResult['error'] ?? 'Unknown error');
            }
        }
    }
}
?>


<?php if ($bookingSuccess): ?>
<!-- ═══ Booking Confirmation ═══ -->
<section class="booking-confirmation">
    <div class="container">
        <div class="confirm-box">
            <div class="confirm-icon"><i class="fas fa-check-circle"></i></div>
            <h1>Booking Confirmed!</h1>
            <p>Your flight has been booked successfully.</p>
            <div class="confirm-details">
                <div class="confirm-row">
                    <span>Booking Reference</span>
                    <strong><?php echo $_SESSION['last_booking_ref']; ?></strong>
                </div>
                <div class="confirm-row">
                    <span>Flight</span>
                    <strong><?php echo $flight['airline_name'] . ' ' . $flight['flight_number']; ?></strong>
                </div>
                <div class="confirm-row">
                    <span>Route</span>
                    <strong><?php echo $searchParams['origin'] . ' → ' . $searchParams['destination']; ?></strong>
                </div>
                <div class="confirm-row">
                    <span>Date</span>
                    <strong><?php echo date('D, M d Y', strtotime($searchParams['departure_date'])); ?></strong>
                </div>
                <div class="confirm-row">
                    <span>Amount Paid</span>
                    <strong><?php echo formatPrice($totalAmount); ?></strong>
                </div>
            </div>
            <p class="confirm-email"><i class="fas fa-envelope"></i> Confirmation sent to <?php echo $_SESSION['user_email']; ?></p>
            <div class="confirm-actions">
                <a href="<?php echo $base; ?>/my-bookings.php" class="btn-primary">View My Bookings</a>
                <a href="<?php echo $base; ?>/" class="btn-outline">Search More Flights</a>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
<!-- ═══ Checkout Form ═══ -->
<section class="checkout-page">
    <div class="container">
        <div class="checkout-layout">


            <!-- Left: Forms -->
            <div class="checkout-main">
                <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo $base; ?>/checkout.php?flight=<?php echo $flightIdx; ?>" id="checkoutForm" novalidate>
                    <?php echo csrfField(); ?>

                    <!-- Flight Summary Card -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-plane"></i> Flight Summary</h2>
                        <div class="flight-summary-card">
                            <div class="fsc-airline">
                                <strong><?php echo $flight['airline_name']; ?></strong>
                                <span><?php echo $flight['flight_number']; ?></span>
                            </div>
                            <div class="fsc-route">
                                <span><?php echo $flight['departure_time']; ?> <small><?php echo $searchParams['origin']; ?></small></span>
                                <i class="fas fa-long-arrow-alt-right"></i>
                                <span><?php echo $flight['arrival_time']; ?> <small><?php echo $searchParams['destination']; ?></small></span>
                            </div>
                            <div class="fsc-meta">
                                <span><i class="fas fa-clock"></i> <?php echo $flight['duration']; ?></span>
                                <span><i class="fas fa-calendar"></i> <?php echo date('D, M d', strtotime($searchParams['departure_date'])); ?></span>
                                <span><?php echo $flight['stops'] == 0 ? 'Non-stop' : $flight['stops'] . ' stop(s)'; ?></span>
                            </div>
                        </div>
                    </div>


                    <!-- Passenger Details -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-users"></i> Passenger Details</h2>
                        <?php
                        $paxIdx = 0;
                        for ($a = 0; $a < $adults; $a++, $paxIdx++):
                        ?>
                        <div class="pax-form-block">
                            <h4>Adult <?php echo $a + 1; ?></h4>
                            <input type="hidden" name="pax_type_<?php echo $paxIdx; ?>" value="adult">
                            <div class="form-row-3">
                                <div class="form-group">
                                    <label>Title *</label>
                                    <select name="pax_title_<?php echo $paxIdx; ?>" required>
                                        <option value="Mr">Mr</option>
                                        <option value="Mrs">Mrs</option>
                                        <option value="Ms">Ms</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" name="pax_fname_<?php echo $paxIdx; ?>" required placeholder="As on ID">
                                </div>
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" name="pax_lname_<?php echo $paxIdx; ?>" required placeholder="As on ID">
                                </div>
                            </div>
                            <div class="form-row-3">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="pax_dob_<?php echo $paxIdx; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="pax_gender_<?php echo $paxIdx; ?>">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Passport No.</label>
                                    <input type="text" name="pax_passport_<?php echo $paxIdx; ?>" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>

                        <?php for ($c = 0; $c < $children; $c++, $paxIdx++): ?>
                        <div class="pax-form-block">
                            <h4>Child <?php echo $c + 1; ?> <small>(2-11 yrs)</small></h4>
                            <input type="hidden" name="pax_type_<?php echo $paxIdx; ?>" value="child">
                            <div class="form-row-3">
                                <div class="form-group">
                                    <label>Title</label>
                                    <select name="pax_title_<?php echo $paxIdx; ?>">
                                        <option value="Master">Master</option>
                                        <option value="Miss">Miss</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" name="pax_fname_<?php echo $paxIdx; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" name="pax_lname_<?php echo $paxIdx; ?>" required>
                                </div>
                            </div>
                            <div class="form-row-3">
                                <div class="form-group">
                                    <label>Date of Birth *</label>
                                    <input type="date" name="pax_dob_<?php echo $paxIdx; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Gender</label>
                                    <select name="pax_gender_<?php echo $paxIdx; ?>">
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group"></div>
                            </div>
                        </div>
                        <?php endfor; ?>

                        <?php for ($inf = 0; $inf < $infants; $inf++, $paxIdx++): ?>
                        <div class="pax-form-block">
                            <h4>Infant <?php echo $inf + 1; ?> <small>(Under 2 yrs)</small></h4>
                            <input type="hidden" name="pax_type_<?php echo $paxIdx; ?>" value="infant">
                            <div class="form-row-3">
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" name="pax_fname_<?php echo $paxIdx; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" name="pax_lname_<?php echo $paxIdx; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth *</label>
                                    <input type="date" name="pax_dob_<?php echo $paxIdx; ?>" required>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>


                    <!-- Payment Details -->
                    <div class="checkout-section">
                        <h2><i class="fas fa-credit-card"></i> Payment Details</h2>
                        <p class="secure-badge"><i class="fas fa-lock"></i> Secured by Authorize.Net | PCI-DSS Compliant</p>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Cardholder First Name *</label>
                                <input type="text" name="billing_fname" required placeholder="First name on card">
                            </div>
                            <div class="form-group">
                                <label>Cardholder Last Name *</label>
                                <input type="text" name="billing_lname" required placeholder="Last name on card">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Card Number *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-credit-card"></i>
                                <input type="text" name="card_number" id="cardNumber" required placeholder="1234 5678 9012 3456" maxlength="19" autocomplete="cc-number">
                            </div>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label>Expiry (MM/YY) *</label>
                                <input type="text" name="card_expiry" required placeholder="MM/YY" maxlength="5" autocomplete="cc-exp">
                            </div>
                            <div class="form-group">
                                <label>CVV *</label>
                                <input type="password" name="card_cvv" required placeholder="***" maxlength="4" autocomplete="cc-csc">
                            </div>
                            <div class="form-group">
                                <label>ZIP Code</label>
                                <input type="text" name="billing_zip" placeholder="10001">
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Billing Address</label>
                                <input type="text" name="billing_address" placeholder="Street address">
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="billing_city" placeholder="City">
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="billing_state" placeholder="State">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <select name="billing_country">
                                    <option value="US">United States</option>
                                    <option value="IN">India</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                </select>
                            </div>
                        </div>

                        <div class="payment-cards-accepted">
                            <span>We Accept:</span>
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-amex"></i>
                            <i class="fab fa-cc-discover"></i>
                        </div>
                    </div>

                    <div class="checkout-agree">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree" value="1" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="<?php echo $base; ?>/terms-conditions.php" target="_blank">Terms</a>, <a href="<?php echo $base; ?>/privacy-policy.php" target="_blank">Privacy Policy</a> & <a href="<?php echo $base; ?>/refund-policy.php" target="_blank">Refund Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn-pay-now" id="payBtn">
                        <i class="fas fa-lock"></i> Pay <?php echo formatPrice($totalAmount); ?> & Confirm Booking
                    </button>
                </form>
            </div>


            <!-- Right: Price Summary Sidebar -->
            <aside class="checkout-sidebar">
                <div class="price-summary-box">
                    <h3>Price Summary</h3>
                    <div class="ps-flight">
                        <strong><?php echo $flight['airline_name']; ?></strong>
                        <span><?php echo $flight['flight_number']; ?></span>
                        <p><?php echo $searchParams['origin'] . ' → ' . $searchParams['destination']; ?></p>
                        <p><?php echo date('D, M d', strtotime($searchParams['departure_date'])); ?> | <?php echo $flight['departure_time']; ?></p>
                    </div>
                    <hr>
                    <div class="ps-breakdown">
                        <?php if ($adults > 0): ?>
                        <div class="ps-row">
                            <span>Adult x <?php echo $adults; ?></span>
                            <span><?php echo formatPrice($flight['total_price'] * $adults); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($children > 0): ?>
                        <div class="ps-row">
                            <span>Child x <?php echo $children; ?></span>
                            <span><?php echo formatPrice($flight['total_price'] * $children); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($infants > 0): ?>
                        <div class="ps-row">
                            <span>Infant x <?php echo $infants; ?></span>
                            <span><?php echo formatPrice($flight['total_price'] * 0.1 * $infants); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                    <div class="ps-total">
                        <span>Total Amount</span>
                        <strong><?php echo formatPrice($totalAmount); ?></strong>
                    </div>
                    <p class="ps-note"><i class="fas fa-shield-alt"></i> Your payment is 100% secure</p>
                </div>

                <div class="checkout-help">
                    <h4><i class="fas fa-headset"></i> Need Help?</h4>
                    <p>Call us at <strong><?php echo SITE_PHONE; ?></strong></p>
                    <p>or email <strong><?php echo SITE_EMAIL; ?></strong></p>
                </div>
            </aside>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
