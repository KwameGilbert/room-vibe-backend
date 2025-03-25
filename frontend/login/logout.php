<?php
// Start the session if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all of the session variables.
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Finally, destroy the session.
if(session_destroy()){
    $response = [
        "success" => true
    ];
};
header('Content-Type: application/json');
echo json_encode($response);