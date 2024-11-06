<?php
function checkGameToken($conn) {
    $token = '';
    $username = '';

    // Check if token is stored in cookies
    if (isset($_COOKIE['game_token'])) {
        $token = $_COOKIE['game_token'];

        // Fetch the username based on the token
        $sql = "SELECT username FROM users WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($username);
        $stmt->fetch();

        if ($stmt->num_rows == 0) {
            // If token is invalid, clear the cookie
            setcookie('game_token', '', time() - 3600, '/');
            $token = '';
        }

        $stmt->close();
    }

    return [$token, $username];
}
?>
