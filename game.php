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
