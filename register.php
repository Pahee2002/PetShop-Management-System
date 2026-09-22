<?php

require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {

        $message = "Please fill all fields.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = "Email already exists.";

        } else {

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role)
                 VALUES (?, ?, ?, 'customer')"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $hashed_password
            );

            if ($stmt->execute()) {

                $message = "Registration successful! You can login now.";

            } else {

                $message = "Registration failed.";

            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - PetCare</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-logo">
            🐾
        </div>

        <h2>Create Account</h2>

        <p>Join PetCare and shop for your favourite pets.</p>

        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Enter your full name"
                    required
                >

            </div>

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
                    placeholder="Create a password"
                    required
                >

            </div>

            <button type="submit" class="btn">
                CREATE ACCOUNT
            </button>

        </form>

        <p style="margin-top: 25px; margin-bottom: 0;">

            Already have an account?

            <a href="login.php">
                Login here
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