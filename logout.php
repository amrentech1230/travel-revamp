<?php
/**
 * Logout - TravenzoTravel
 */
require_once 'config/config.php';

session_unset();
session_destroy();

// Start new session to set flash
session_start();
setFlash('success', 'You have been logged out successfully.');
redirect('/');
