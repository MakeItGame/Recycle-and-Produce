<?php
// Database connection
$servername = "localhost";
$username = "webadmin";
$password = '4fwe484eW4"8§%4§8/358&"§$&§$T';
$dbname = "recycle_and_produce";

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
    <style>
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow: hidden;
    font-family: Arial, sans-serif;
}

.video-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: -1;
}

.navbar {
    background: #333;
    color: white;
    padding: 10px;
    text-align: left;
}

.navbar a {
    color: white;
    padding: 14px 20px;
    text-decoration: none;
    display: inline-block;
}

.navbar a:hover {
    background: #ddd;
    color: black;
}

.container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: calc(100% - 50px); /* Adjust for navbar height */
    padding: 20px;
    box-sizing: border-box;
}

.box {
    background: rgba(0, 0, 0, 0.5);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    width: 90%;
    max-width: 600px;
    margin: 10px;
    color: white;
    text-align: center;
    box-sizing: border-box;
    flex-grow: 1;
}

.start-game-btn {
    width: 90%;
    max-width: 600px;
    padding: 15px;
    background: #4caf50;
    border: none;
    border-radius: 5px;
    color: white;
    font-size: 20px;
    cursor: pointer;
    margin: 20px;
}

.start-game-btn:hover {
    background: #45a049;
}

.content {
    padding: 15px;
}

h2 {
    font-size: 1.6em;
    margin: 10px 0;
}

p {
    font-size: 1em;
    margin: 10px 0;
}

@media (max-width: 600px) {
    .box, .start-game-btn {
        width: 100%;
    }
    .content {
        padding: 10px;
    }
    h2 {
        font-size: 1.5em;
    }
    p {
        font-size: 1em;
    }
}
    </style>
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
            <form method="get" action="game.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <button type="submit" class="start-game-btn">Start Game</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
