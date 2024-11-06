<?php
// Database connection
require_once 'config.php';
require_once 'cookie_check.php';

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

list($token, $username) = checkGameToken($conn);

function generateToken() {
    return bin2hex(random_bytes(32));
}

$registration_success = false;
$message = '';
$cookie_token = $_COOKIE['game_token'] ?? '';

if ($cookie_token && !$token) {
    $message = "Token not found in the database. Please try again or register. Here is your token: $cookie_token";
    echo "<script>alert('$message');</script>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Username already taken. Please choose another one.";
    } else {
        do {
            $token = generateToken();
            $sql = "SELECT id FROM users WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->store_result();
        } while ($stmt->num_rows > 0);

        $sql = "INSERT INTO users (username, token) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $token);
        if ($stmt->execute()) {
            $registration_success = true;
            setcookie('game_token', $token, time() + (86400 * 30), '/');
            header("Location: index.php");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }
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
    <title>Register</title>
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
            background: #00fff7;
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
        <?php if (!$registration_success): ?>
            <h2>Register</h2>
            <form method="post" action="register.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
                <?php if (isset($message)): ?>
                    <p><?php echo $message; ?></p>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <form method="get" action="game.php">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <div class="form-group">
                    <button type="submit">Enter</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
