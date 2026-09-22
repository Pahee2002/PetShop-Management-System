<?php

session_start();
require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];
                $_SESSION["user_role"] = $user["role"];

                // Admin → Admin Dashboard
                if ($user["role"] == "admin") {

                    header("Location: admin_dashboard.php");
                    exit;

                } else {

                    // Customer → Home Page
                    header("Location: index.php");
                    exit;
                }

            } else {

                $message = "Invalid email or password.";

            }

        } else {

            $message = "Invalid email or password.";

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

    <title>Login - PetCare</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-logo">
            🐾
        </div>

        <h2>Welcome Back!</h2>

        <p>Login to your PetCare account</p>

        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>

            <button type="submit" class="btn">
                LOGIN
            </button>

        </form>

        <p style="margin-top: 25px; margin-bottom: 0;">

            Don't have an account?

            <a href="register.php">
                Register here
            </a>

        </p>

        <p style="margin-top: 15px;">

            <a href="index.php">
                ← Back to Home
            </a>

        </p>

    </div>

</div>

</body>

</html>