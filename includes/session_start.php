<?php
/**
 * MediConnect Session Start and Initialization
 * Ensures session is started safely and sets the default 'guest' role.
 */

// Check if a session has NOT been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Default role is 'guest' for unauthenticated users.
// This is used by the navbar to determine what links to show.
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'guest';
}
?>
