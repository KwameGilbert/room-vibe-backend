<?php
/**
 * Authentication helper functions for manager portal
 */

/**
 * Verify if manager is logged in, if not redirect to login page
 */
function requireManagerLogin() {
    session_start();
    if (!isset($_SESSION['manager_id'])) {
        header('Location: ../login.php');
        exit();
    }
}

/**
 * Get manager ID from session
 * 
 * @return int|null Manager ID or null if not logged in
 */
function getManagerId() {
    return $_SESSION['manager_id'] ?? null;
}

/**
 * Get manager name from session
 * 
 * @return string|null Manager name or null if not logged in
 */
function getManagerName() {
    return $_SESSION['manager_name'] ?? null;
}

/**
 * Get managed hostel ID
 * 
 * @param PDO $conn Database connection
 * @param int $managerId Manager ID
 * @return int|null Hostel ID or null if no hostel is managed
 */
function getManagedHostelId($conn, $managerId) {
    $stmt = $conn->prepare("SELECT id FROM hostel WHERE manager_id = ? LIMIT 1");
    $stmt->execute([$managerId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['id'] : null;
}

/**
 * Get managed hostel details
 * 
 * @param PDO $conn Database connection
 * @param int $managerId Manager ID
 * @return array|null Hostel details or null if no hostel is managed
 */
function getManagedHostelDetails($conn, $managerId) {
    $stmt = $conn->prepare("SELECT * FROM hostel WHERE manager_id = ? LIMIT 1");
    $stmt->execute([$managerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}