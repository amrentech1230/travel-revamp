<?php
/**
 * Airport Search AJAX Handler - TravenzoTravel
 * Returns matching airports for autocomplete from database
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=3600'); // Cache results for 1 hour

$query = trim($_GET['q'] ?? '');

// Require at least 2 characters to search
if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $db = getDB();
    $search = '%' . $query . '%';
    
    // Search by IATA code, city name, airport name, or country
    // Prioritize exact IATA matches first, then city matches, then name/country matches
    $sql = "SELECT iata, name, city, country 
            FROM airports 
            WHERE iata LIKE ? OR city LIKE ? OR name LIKE ? OR country LIKE ?
            ORDER BY 
                CASE 
                    WHEN iata = ? THEN 1
                    WHEN iata LIKE ? THEN 2
                    WHEN city LIKE ? THEN 3
                    ELSE 4
                END,
                city ASC
            LIMIT 10";
    
    $iataExact = strtoupper($query);
    $iataLike = strtoupper($query) . '%';
    $cityLike = $query . '%';
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$search, $search, $search, $search, $iataExact, $iataLike, $cityLike]);
    $results = $stmt->fetchAll();
    
    echo json_encode($results);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed']);
}
