<?php
/**
 * Main Configuration - TravenzoTravel
 */

// Start output buffering to prevent "headers already sent" errors
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site Configuration
define('SITE_NAME', 'TravenzoTravel');
define('SITE_URL', 'https://travenzotravel.com');
define('SITE_EMAIL', 'support@travenzotravel.com');
define('SITE_PHONE', '+1-888-TRAVENSO');
define('SITE_ADDRESS', '500 Fifth Avenue, Suite 1200, New York, NY 10036, USA');
define('BASE_PATH', '/travel-revamp'); // Base URL path for localhost

// Mondee API Configuration
define('MONDEE_API_URL', 'https://api.mondee.com/v2');
define('MONDEE_API_KEY', 'YOUR_MONDEE_API_KEY_HERE');
define('MONDEE_API_SECRET', 'YOUR_MONDEE_API_SECRET_HERE');
define('MONDEE_AGENT_ID', 'YOUR_MONDEE_AGENT_ID_HERE');

// Authorize.Net Payment Gateway
define('AUTHNET_LOGIN_ID', 'YOUR_AUTHNET_LOGIN_ID');
define('AUTHNET_TRANSACTION_KEY', 'YOUR_AUTHNET_TRANSACTION_KEY');
define('AUTHNET_SANDBOX', true); // Set false in production
define('AUTHNET_ENDPOINT', AUTHNET_SANDBOX
    ? 'https://apitest.authorize.net/xml/v1/request.api'
    : 'https://api.authorize.net/xml/v1/request.api');

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'travenzotravel');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CLASSES_PATH', ROOT_PATH . 'classes/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('America/New_York');

// Autoload classes
spl_autoload_register(function ($class) {
    $file = CLASSES_PATH . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Database connection (PDO)
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// ─── Helper Functions ─────────────────────────────────────────────

function redirect($url) {
    header("Location: $url");
    exit;
}

function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        setFlash('error', 'Please login to continue.');
        redirect(BASE_PATH . '/login.php');
    }
}

function url($path = '') {
    return BASE_PATH . '/' . ltrim($path, '/');
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
    ];
}

function csrfToken() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfField() {
    return '<input type="hidden" name="_token" value="' . csrfToken() . '">';
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $msg];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function formatPrice($amount) {
    return '$' . number_format((float)$amount, 2);
}

function generateRef($prefix = 'TRV') {
    return $prefix . strtoupper(substr(uniqid(), -8));
}
