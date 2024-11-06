<?php
// Include configuration and cookie check files
require_once 'config.php';
require_once 'cookie_check.php';

// Create connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check the game token
list($token, $username) = checkGameToken($conn);

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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-left {
            display: flex;
            align-items: center;
        }

        .nav-right {
            display: flex;
            align-items: center;
        }

        .nav-button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 10px;
        }

        .nav-button:hover {
            background: #45a049;
        }

        .content-section {
            display: none;
            padding: 20px;
        }

        .content-section.active {
            display: block;
        }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .inventory-item {
            background: #222;
            padding: 10px;
            border-radius: 10px;
            color: white;
            text-align: center;
        }

        .inventory-item img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .inventory-bar {
            position: fixed;
            top: 50px;
            right: 0;
            width: 300px;
            height: calc(100% - 50px);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            overflow-y: auto;
            transform: translateX(0);
            transition: transform 0.3s ease-in-out;
            z-index: 2;
            padding: 10px;
            box-sizing: border-box;
        }

        .inventory-bar.minimized {
            transform: translateX(100%);
        }

        .inventory-toggle-btn {
            position: fixed;
            top: 50px;
            right: 0;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            z-index: 3;
        }

        .inventory-item {
            margin-bottom: 10px;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <video class="video-bg" autoplay muted loop>
        <source src="background.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="navbar">
        <div class="nav-left">
            <button class="nav-button" data-target="home">Home</button>
            <button class="nav-button" data-target="inventory">Inventory</button>
            <?php if ($token): ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <?php else: ?>
                <a href="login.php" class="nav-button">Login</a>
                <a href="register.php" class="nav-button">Register</a>
            <?php endif; ?>
        </div>
        <div class="nav-right">
            <button class="nav-button" id="copy-token-btn">Copy Token</button>
            <button class="nav-button" id="save-game-btn">Save Game</button>
        </div>
    </div>

    <button class="inventory-toggle-btn" id="inventory-toggle-btn">☰</button>

    <div class="inventory-bar minimized" id="inventory-bar">
        <h3>Inventory</h3>
        <div class="inventory-grid">
            <?php for ($i = 1; $i <= 20; $i++): ?>
                <div class="inventory-item">
                    <img src="placeholder.png" alt="Item Image">
                    <h4>Item <?php echo $i; ?></h4>
                    <p>Lorem ipsum dolor sit amet.</p>
                    <p>Count: <?php echo rand(1, 100); ?></p>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <div id="content">
        <section id="home" class="content-section active">
            <h2>Welcome to EcoTycoon: Recycle and Prosper!</h2>
            <p>Step into the ultimate recycling simulation where your eco-vision transforms into a flourishing empire!</p>
        </section>
        <section id="inventory" class="content-section">
            <h2>Inventory</h2>
            <div class="inventory-grid">
                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <div class="inventory-item">
                        <img src="placeholder.png" alt="Item Image">
                        <h4>Item <?php echo $i; ?></h4>
                        <p>Lorem ipsum dolor sit amet.</p>
                        <p>Count: <?php echo rand(1, 100); ?></p>
                    </div>
                <?php endfor; ?>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.nav-button[data-target]');
            const sections = document.querySelectorAll('.content-section');
            const inventoryBar = document.getElementById('inventory-bar');
            const inventoryToggleBtn = document.getElementById('inventory-toggle-btn');

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.getAttribute('data-target');
                    sections.forEach(section => section.classList.remove('active'));
                    document.getElementById(target).classList.add('active');
                });
            });

            document.getElementById('save-game-btn').addEventListener('click', () => {
                console.log('Save game functionality will be implemented later.');
            });

            document.getElementById('copy-token-btn').addEventListener('click', () => {
                const token = "<?php echo $token; ?>";
                navigator.clipboard.writeText(token).then(() => {
                    alert('Token copied to clipboard!');
                }).catch(err => {
                    console.error('Could not copy token: ', err);
                });
            });

            inventoryToggleBtn.addEventListener('click', () => {
                inventoryBar.classList.toggle('minimized');
            });

            document.getElementById('home').classList.add('active');
        });
    </script>
</body>
</html>
