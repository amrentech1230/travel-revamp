<?php
/**
 * Airport Search AJAX Handler - TravenzoTravel
 * Returns matching airports for autocomplete
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$query = sanitize($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = getDB();
    $search = '%' . $query . '%';
    $stmt = $db->prepare("SELECT iata, name, city, country FROM airports WHERE iata LIKE ? OR city LIKE ? OR name LIKE ? OR country LIKE ? ORDER BY CASE WHEN iata LIKE ? THEN 1 WHEN city LIKE ? THEN 2 ELSE 3 END LIMIT 10");
    $directMatch = $query . '%';
    $stmt->execute([$search, $search, $search, $search, $directMatch, $directMatch]);
    $results = $stmt->fetchAll();
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
