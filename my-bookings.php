<?php
/**
 * My Bookings Page - TravenzoTravel
 */
$pageTitle = 'My Bookings';
require_once 'includes/header.php';

requireLogin();

// Fetch user bookings
$db = getDB();
$stmt = $db->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<section class="page-banner">
    <div class="container">
        <h1>My <span>Bookings</span></h1>
        <p>View and manage all your flight bookings</p>
    </div>
</section>

<section class="bookings-section">
    <div class="container">
        <?php if (empty($bookings)): ?>
        <div class="no-results-box">
            <i class="fas fa-ticket-alt"></i>
            <h3>No Bookings Yet</h3>
            <p>You haven't made any flight bookings. Start searching for flights now!</p>
            <a href="/" class="btn-primary">Search Flights</a>
        </div>
        <?php else: ?>
        <div class="bookings-list">
            <?php foreach ($bookings as $booking): ?>
            <div class="booking-card">
                <div class="booking-card-header">
                    <div class="booking-ref">
                        <span class="ref-label">Booking Ref:</span>
                        <strong><?php echo $booking['booking_ref']; ?></strong>
                    </div>
                    <span class="booking-status status-<?php echo $booking['status']; ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                </div>
                <div class="booking-card-body">
                    <div class="booking-route">
                        <div class="booking-city">
                            <strong><?php echo $booking['origin_code']; ?></strong>
                            <small><?php echo $booking['departure_time']; ?></small>
                        </div>
                        <div class="booking-arrow">
                            <span class="booking-airline"><?php echo $booking['airline_name']; ?> (<?php echo $booking['flight_number']; ?>)</span>
                            <div class="arrow-line"><i class="fas fa-plane"></i></div>
                            <span class="booking-duration"><?php echo $booking['duration']; ?> | <?php echo $booking['stops'] == 0 ? 'Non-stop' : $booking['stops'] . ' stop(s)'; ?></span>
                        </div>
                        <div class="booking-city">
                            <strong><?php echo $booking['destination_code']; ?></strong>
                            <small><?php echo $booking['arrival_time']; ?></small>
                        </div>
                    </div>
                    <div class="booking-meta">
                        <span><i class="fas fa-calendar"></i> <?php echo date('D, M d Y', strtotime($booking['departure_date'])); ?></span>
                        <span><i class="fas fa-users"></i> <?php echo $booking['adults'] + $booking['children'] + $booking['infants']; ?> Traveler(s)</span>
                        <span><i class="fas fa-chair"></i> <?php echo ucfirst(str_replace('_', ' ', $booking['cabin_class'])); ?></span>
                    </div>
                </div>
                <div class="booking-card-footer">
                    <div class="booking-price">
                        <span>Total Paid:</span>
                        <strong><?php echo formatPrice($booking['total_amount']); ?></strong>
                    </div>
                    <div class="booking-actions">
                        <?php if ($booking['mondee_pnr']): ?>
                        <span class="pnr-badge"><i class="fas fa-barcode"></i> PNR: <?php echo $booking['mondee_pnr']; ?></span>
                        <?php endif; ?>
                        <?php if ($booking['status'] === 'confirmed'): ?>
                        <a href="/contact.php?ref=<?php echo $booking['booking_ref']; ?>" class="btn-outline-sm">Request Cancel</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
