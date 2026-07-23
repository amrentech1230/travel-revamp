<?php
/**
 * Newsletter Subscription AJAX Handler - TravenzoTravel
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$email = sanitize($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO newsletter (email) VALUES (?) ON DUPLICATE KEY UPDATE is_active = 1");
    $stmt->execute([$email]);
    echo json_encode(['success' => true, 'message' => 'Thank you! You are now subscribed.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
