<?php

session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["user_role"] != "customer") {
    header("Location: admin_dashboard.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT name, email, created_at
     FROM users
     WHERE id = ? AND role = 'customer'"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: logout.php");
    exit;
}

$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Account - PetCare</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<nav class="navbar">

    <a href="customer_dashboard.php" class="logo">
        🐾 Pet<span>Care</span>
    </a>

    <ul class="nav-links">

        <li>
            <a href="customer_dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="shop.php">
                Shop
            </a>
        </li>

        <li>
            <a href="cart.php">
                🛒 Cart
            </a>
        </li>

        <li>
            <a href="orders.php">
                📦 My Orders
            </a>
        </li>

        <li>
            <a href="logout.php" class="login-btn">
                LOGOUT
            </a>
        </li>

    </ul>

</nav>


<div class="dashboard">

    <div class="dashboard-header">

        <h1>
            My Account 👤
        </h1>

        <p>
            View your PetCare account information.
        </p>

    </div>


    <div class="account-card">

        <div class="account-icon">
            👤
        </div>

        <h2>
            <?php echo htmlspecialchars($user["name"]); ?>
        </h2>


        <div class="account-info">

            <div class="account-row">

                <strong>Name</strong>

                <span>
                    <?php echo htmlspecialchars($user["name"]); ?>
                </span>

            </div>


            <div class="account-row">

                <strong>Email</strong>

                <span>
                    <?php echo htmlspecialchars($user["email"]); ?>
                </span>

            </div>


            <div class="account-row">

                <strong>Account Type</strong>

                <span>
                    Customer
                </span>

            </div>


            <div class="account-row">

                <strong>Joined Date</strong>

                <span>
                    <?php
                    echo date(
                        "d M Y",
                        strtotime($user["created_at"])
                    );
                    ?>
                </span>

            </div>

        </div>


        <div class="account-buttons">

            <a href="shop.php" class="btn">
                🛒 Continue Shopping
            </a>

            <a href="orders.php" class="btn">
                📦 My Orders
            </a>

        </div>

    </div>

</div>

</body>

</html>