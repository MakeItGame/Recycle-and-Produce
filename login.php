<?php
// Database connection
require_once 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$token_valid = false;
$message = '';
$username = '';
$cookie_token = '';

if (isset($_COOKIE['game_token'])) {
    $cookie_token = $_COOKIE['game_token'];

    // Check if token exists in database
    $sql = "SELECT username FROM users WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cookie_token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Token is valid, redirect to index.php
        header("Location: index.php");
        exit();
    } else {
        // Token is invalid, delete the cookie
        setcookie('game_token', '', time() - 3600, '/');
        $message = "Token not found in the database. Please try again or register. Here is your token: $cookie_token";
        echo "<script>alert('$message');</script>";
    }

    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];

    // Check if token exists
    $sql = "SELECT username FROM users WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        // Token is valid, set cookie and redirect to index.php
        setcookie('game_token', $token, time() + (86400 * 30), '/'); // 30 days
        $token_valid = true;
        header("Location: index.php");
        exit();
    } else {
        $message = "Invalid token. Please try again or register.";
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
    <title>Login</title>
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

        .container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            width: calc(100% - 40px);
            max-width: 400px;
            margin: auto;
            color: white;
            z-index: 1;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            outline: none;
            box-sizing: border-box;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background: #4caf50;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background: #45a049;
        }

        .form-group a.button-link {
            display: inline-block;
            width: 100%;
            padding: 10px;
            background: #4caf50;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            margin-top: 10px;
        }

        .form-group a.button-link:hover {
            background: #45a049;
        }

        @media (max-width: 768px) {
            .container {
                width: calc(100% - 40px);
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <video class="video-bg" autoplay muted loop>
        <source src="background.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>

    <div class="container">
        <?php if (!$token_valid): ?>
            <h2>Login</h2>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="token">Token:</label>
                    <input type="text" id="token" name="token" required>
                </div>
                <div class="form-group">
                    <button type="submit">Play</button>
                </div>
                <?php if (!empty($message)): ?>
                    <p><?php echo $message; ?></p>
                    <a class="button-link" href="register.php">Register</a>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p>Logged in as <?php echo htmlspecialchars($username); ?></p>
            <form method="get" action="game.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <button type="submit">Enter</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
