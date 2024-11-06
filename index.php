<?php
// Database connection
require_once 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <video class="video-bg" autoplay muted loop>
        <source src="background.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="navbar">
        <?php if ($token): ?>
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="box">
            <div class="content">
                <h2>Welcome to EcoTycoon: Recycle and Prosper!</h2>
                <p>Step into the ultimate recycling simulation where your eco-vision transforms into a flourishing empire!</p>
                <p>🌍✨</p>
                <p><strong>Collect. Process. Prosper.</strong></p>
                <p>As an ambitious recycler, embark on a journey to create the most efficient recycling plant the world has ever seen. Gather vast arrays of recyclable materials from bustling cities, serene suburbs, and pristine wilderness.</p>
                <p><strong>Innovate Your Process</strong></p>
                <p>Unlock cutting-edge technologies and state-of-the-art machinery to enhance your recycling prowess. Turn waste into precious materials that can be sold for profit.</p>
                <p><strong>Build Your Empire</strong></p>
                <p>Earn the means to expand your operations. Acquire new machinery, develop advanced processing techniques, and explore new possibilities in material innovation.</p>
                <p><strong>Explore New Possibilities</strong></p>
                <p>Repurpose materials into new and exciting products. Transform old tires into playground surfaces, plastic waste into stylish furniture, and more. Your imagination is the only limit!</p>
                <p><strong>Eco-Friendly Adventure</strong></p>
                <p>This isn't just a game—it's a mission to create a sustainable future. Each decision not only grows your empire but also contributes to a greener planet.</p>
                <h3>Important! The game was made to be played on PC with FHD 16:9 (Other Resolutions/Devices may cause Problems)</h3>
            </div>
        </div>
        <?php if ($token): ?>
            <button type="button" class="start-game-btn" onclick="window.location.href='game.php'">Start Game</button>
        <?php endif; ?>
    </div>
</body>
</html>
