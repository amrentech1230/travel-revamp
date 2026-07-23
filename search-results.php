<?php
/**
 * Flight Search Results - TravenzoTravel
 * Displays Mondee API results with filters & sort
 */
$pageTitle = 'Flight Search Results';
require_once 'includes/header.php';

// Get search params
$tripType   = sanitize($_GET['trip_type'] ?? 'oneway');
$origin     = sanitize($_GET['origin_code'] ?? $_GET['origin'] ?? '');
$destination= sanitize($_GET['destination_code'] ?? $_GET['destination'] ?? '');
$depDate    = sanitize($_GET['departure_date'] ?? '');
$retDate    = sanitize($_GET['return_date'] ?? '');
$adults     = max(1, (int)($_GET['adults'] ?? 1));
$children   = max(0, (int)($_GET['children'] ?? 0));
$infants    = max(0, (int)($_GET['infants'] ?? 0));
$cabinClass = sanitize($_GET['cabin_class'] ?? 'economy');

// Validate
if (empty($origin) || empty($destination) || empty($depDate)) {
    setFlash('error', 'Please enter valid search details.');
    redirect('/');
}

// Search via Mondee API
$mondee = new MondeeAPI();
$searchParams = [
    'trip_type'      => $tripType,
    'origin'         => $origin,
    'destination'    => $destination,
    'departure_date' => $depDate,
    'return_date'    => $retDate,
    'adults'         => $adults,
    'children'       => $children,
    'infants'        => $infants,
    'cabin_class'    => $cabinClass,
];

$results = $mondee->searchFlights($searchParams);
$flights = $results['flights'] ?? [];
$totalFlights = count($flights);

// Store in session for checkout
$_SESSION['search_params'] = $searchParams;
$_SESSION['search_results'] = $flights;

// Get unique airlines for filter
$airlines = [];
foreach ($flights as $f) {
    $airlines[$f['airline_code']] = $f['airline_name'];
}

$cabinLabels = [
    'economy' => 'Economy', 'premium_economy' => 'Premium Economy',
    'business' => 'Business', 'first' => 'First Class'
];
?>

<!-- ═══ Results Header ═══ -->
<section class="results-header">
    <div class="container">
        <div class="results-route">
            <h1>
                <span class="route-code"><?php echo $origin; ?></span>
                <i class="fas fa-long-arrow-alt-right"></i>
                <span class="route-code"><?php echo $destination; ?></span>
            </h1>
            <div class="results-meta">
                <span><i class="fas fa-calendar-alt"></i> <?php echo date('D, M d', strtotime($depDate)); ?></span>
                <?php if ($tripType === 'roundtrip' && $retDate): ?>
                    <span>— <?php echo date('D, M d', strtotime($retDate)); ?></span>
                <?php endif; ?>
                <span><i class="fas fa-users"></i> <?php echo $adults + $children + $infants; ?> Traveler(s)</span>
                <span><i class="fas fa-chair"></i> <?php echo $cabinLabels[$cabinClass] ?? 'Economy'; ?></span>
                <span class="results-count-badge"><?php echo $totalFlights; ?> flights</span>
            </div>
        </div>

        <!-- Modify Search Bar -->
        <form class="modify-bar" action="/search-results.php" method="GET">
            <input type="text" name="origin_code" value="<?php echo $origin; ?>" placeholder="From" class="modify-input">
            <i class="fas fa-exchange-alt modify-swap"></i>
            <input type="text" name="destination_code" value="<?php echo $destination; ?>" placeholder="To" class="modify-input">
            <input type="date" name="departure_date" value="<?php echo $depDate; ?>" class="modify-input">
            <input type="hidden" name="trip_type" value="<?php echo $tripType; ?>">
            <input type="hidden" name="adults" value="<?php echo $adults; ?>">
            <input type="hidden" name="children" value="<?php echo $children; ?>">
            <input type="hidden" name="infants" value="<?php echo $infants; ?>">
            <input type="hidden" name="cabin_class" value="<?php echo $cabinClass; ?>">
            <button type="submit" class="modify-btn"><i class="fas fa-search"></i> Modify</button>
        </form>
    </div>
</section>

<!-- ═══ Results Body ═══ -->
<section class="results-body">
    <div class="container">
        <div class="results-layout">

            <!-- ─── Sidebar Filters ─── -->
            <aside class="filters-panel" id="filtersPanel">
                <div class="filters-top">
                    <h3><i class="fas fa-sliders-h"></i> Filters</h3>
                    <button class="btn-clear-filters" id="clearFilters">Clear All</button>
                </div>

                <!-- Price -->
                <div class="filter-block">
                    <h4>Price Range</h4>
                    <input type="range" id="priceSlider" min="0" max="2000" value="2000" class="range-input">
                    <div class="range-labels"><span>$0</span><span id="priceVal">$2000</span></div>
                </div>

                <!-- Stops -->
                <div class="filter-block">
                    <h4>Stops</h4>
                    <label class="filter-check"><input type="checkbox" class="stop-filter" value="0" checked> Non-stop</label>
                    <label class="filter-check"><input type="checkbox" class="stop-filter" value="1" checked> 1 Stop</label>
                    <label class="filter-check"><input type="checkbox" class="stop-filter" value="2" checked> 2+ Stops</label>
                </div>

                <!-- Departure Time -->
                <div class="filter-block">
                    <h4>Departure Time</h4>
                    <label class="filter-check"><input type="checkbox" class="time-filter" value="morning" checked> <i class="fas fa-sun"></i> 6AM - 12PM</label>
                    <label class="filter-check"><input type="checkbox" class="time-filter" value="afternoon" checked> <i class="fas fa-cloud-sun"></i> 12PM - 6PM</label>
                    <label class="filter-check"><input type="checkbox" class="time-filter" value="evening" checked> <i class="fas fa-moon"></i> 6PM - 12AM</label>
                    <label class="filter-check"><input type="checkbox" class="time-filter" value="night" checked> <i class="fas fa-star"></i> 12AM - 6AM</label>
                </div>

                <!-- Airlines -->
                <div class="filter-block">
                    <h4>Airlines</h4>
                    <?php foreach ($airlines as $code => $name): ?>
                    <label class="filter-check">
                        <input type="checkbox" class="airline-filter" value="<?php echo $code; ?>" checked>
                        <?php echo $name; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </aside>

            <!-- ─── Flight List ─── -->
            <div class="flights-main">
                <!-- Sort Bar -->
                <div class="sort-bar">
                    <div class="sort-buttons">
                        <button class="sort-btn active" data-sort="price"><i class="fas fa-dollar-sign"></i> Cheapest</button>
                        <button class="sort-btn" data-sort="duration"><i class="fas fa-bolt"></i> Fastest</button>
                        <button class="sort-btn" data-sort="departure"><i class="fas fa-clock"></i> Earliest</button>
                    </div>
                    <button class="btn-mobile-filter" id="mobileFilterBtn"><i class="fas fa-filter"></i> Filters</button>
                </div>

                <!-- Flight Cards -->
                <div class="flight-list" id="flightList">
                    <?php if (empty($flights)): ?>
                        <div class="no-results-box">
                            <i class="fas fa-plane-slash"></i>
                            <h3>No Flights Found</h3>
                            <p>We couldn't find flights for your search. Try different dates or routes.</p>
                            <a href="/" class="btn-primary">Search Again</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($flights as $idx => $flight): ?>
                        <div class="flight-card"
                             data-price="<?php echo $flight['total_price']; ?>"
                             data-duration="<?php echo $flight['duration_mins']; ?>"
                             data-departure="<?php echo $flight['departure_time']; ?>"
                             data-stops="<?php echo $flight['stops']; ?>"
                             data-airline="<?php echo $flight['airline_code']; ?>">

                            <div class="fc-airline">
                                <div class="airline-logo-sm"><?php echo $flight['airline_code']; ?></div>
                                <div class="airline-info">
                                    <strong><?php echo $flight['airline_name']; ?></strong>
                                    <small><?php echo $flight['flight_number']; ?></small>
                                </div>
                            </div>

                            <div class="fc-schedule">
                                <div class="fc-time">
                                    <span class="time-big"><?php echo $flight['departure_time']; ?></span>
                                    <span class="city-code"><?php echo $origin; ?></span>
                                </div>
                                <div class="fc-duration">
                                    <span class="dur-text"><?php echo $flight['duration']; ?></span>
                                    <div class="dur-line"><span></span><i class="fas fa-plane"></i></div>
                                    <span class="stops-text <?php echo $flight['stops'] == 0 ? 'nonstop' : ''; ?>">
                                        <?php echo $flight['stops'] == 0 ? 'Non-stop' : $flight['stops'] . ' Stop(s)'; ?>
                                    </span>
                                </div>
                                <div class="fc-time">
                                    <span class="time-big"><?php echo $flight['arrival_time']; ?></span>
                                    <span class="city-code"><?php echo $destination; ?></span>
                                </div>
                            </div>

                            <div class="fc-price-book">
                                <div class="fc-price">
                                    <span class="price-big"><?php echo formatPrice($flight['total_price']); ?></span>
                                    <small>per person</small>
                                </div>
                                <a href="/checkout.php?flight=<?php echo $idx; ?>" class="btn-book-flight">Book Now</a>
                            </div>

                            <!-- Expandable Details -->
                            <button class="fc-details-toggle" data-idx="<?php echo $idx; ?>">
                                <i class="fas fa-chevron-down"></i> Flight Details
                            </button>
                            <div class="fc-details" id="fcDetails-<?php echo $idx; ?>" style="display:none;">
                                <div class="fc-details-grid">
                                    <div>
                                        <h5>Flight Info</h5>
                                        <p><strong>Aircraft:</strong> <?php echo $flight['aircraft']; ?></p>
                                        <p><strong>Class:</strong> <?php echo $cabinLabels[$cabinClass]; ?></p>
                                        <p><strong>Refundable:</strong> <?php echo $flight['refundable'] ? 'Yes' : 'No'; ?></p>
                                    </div>
                                    <div>
                                        <h5>Fare Breakdown</h5>
                                        <p>Base Fare: <?php echo formatPrice($flight['base_fare']); ?></p>
                                        <p>Taxes & Fees: <?php echo formatPrice($flight['taxes']); ?></p>
                                        <p>Service Fee: <?php echo formatPrice($flight['service_fee']); ?></p>
                                        <p><strong>Total: <?php echo formatPrice($flight['total_price']); ?></strong></p>
                                    </div>
                                    <div>
                                        <h5>Baggage</h5>
                                        <p><i class="fas fa-suitcase"></i> Cabin: <?php echo $flight['cabin_baggage']; ?></p>
                                        <p><i class="fas fa-suitcase-rolling"></i> Check-in: <?php echo $flight['checkin_baggage']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
