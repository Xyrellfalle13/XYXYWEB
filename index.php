<?php
session_start();
require_once "db.php";

$message = "";
$messageType = "";

if (isset($_SESSION["registration_success"])) {
    $message = $_SESSION["registration_success"];
    $messageType = "success";
    unset($_SESSION["registration_success"]);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $message = "Please enter your username and password.";
        $messageType = "error";

    } else {

        $sql = "SELECT id, fullname, username, password
                FROM users
                WHERE username = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["fullname"] = $user["fullname"];
                $_SESSION["username"] = $user["username"];

                header("Location: home.php");
                exit();

            } else {

                $message = "INVALID USERNAME OR PASSWORD";
                $messageType = "error";
            }

        } else {

            $message = "ACCOUNT NOT FOUND IN DATABASE";
            $messageType = "error";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Future System</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="grid"></div>

<div class="login-container">

    <div class="login-card">

        <div class="logo">
            ✦ FUTURE
        </div>

        <h1>WELCOME BACK</h1>

        <p class="subtitle">
            Access the future
        </p>

        <?php if ($message !== ""): ?>

            <div
                class="status-message <?= $messageType ?>"
                id="statusMessage"
            >
                <?= htmlspecialchars($message) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="index.php">

            <div class="input-group">

                <input
                    type="text"
                    name="username"
                    id="username"
                    required
                >

                <label for="username">
                    USERNAME
                </label>

            </div>

            <div class="input-group">

                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                >

                <label for="password">
                    PASSWORD
                </label>

            </div>

            <button
                type="submit"
                class="login-btn"
            >
                LOGIN
            </button>

        </form>

        <p class="register-text">

            Don't have an account?

            <a href="register.php">
                CREATE ACCOUNT
            </a>

        </p>

    </div>

</div>

<script src="script.js"></script>

</body>
</html>