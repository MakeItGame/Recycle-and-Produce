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
    <title>Game Main Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="navbar">
        <button class="nav-button" id="save-game-btn">Save Game</button>
        <div>
            <button class="nav-button" data-target="home">Home</button>
            <button class="nav-button" data-target="inventory">Inventory</button>
            <button class="nav-button" data-target="shop">Shop</button>
            <!-- Add more buttons as needed -->
        </div>
        <?php if ($token): ?>
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
        <?php else: ?>
            <a href="login.php" class="nav-button">Login</a>
            <a href="register.php" class="nav-button">Register</a>
        <?php endif; ?>
    </div>

    <div id="content">
        <section id="home" class="content-section active">
            <h2>Welcome to EcoTycoon: Recycle and Prosper!</h2>
            <p>Step into the ultimate recycling simulation where your eco-vision transforms into a flourishing empire!</p>
            <!-- Add more content as needed -->
        </section>
        <section id="inventory" class="content-section">
            <h2>Inventory</h2>
            <p>Your inventory is empty.</p>
            <!-- Add more content as needed -->
        </section>
        <section id="shop" class="content-section">
            <h2>Shop</h2>
            <p>Browse the shop items.</p>
            <!-- Add more content as needed -->
        </section>
        <!-- Add more sections as needed -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.nav-button[data-target]');
            const sections = document.querySelectorAll('.content-section');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-target');

                    sections.forEach(section => {
                        section.classList.remove('active');
                    });

                    document.getElementById(target).classList.add('active');
                });
            });

            document.getElementById('save-game-btn').addEventListener('click', () => {
                console.log('Save game functionality will be implemented later.');
            });

            // Default to showing the home section
            document.getElementById('home').classList.add('active');
        });
    </script>
</body>
</html>
