<?php

session_start();




if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit;

}




if ($_SESSION["user_role"] != "customer") {

    header("Location: admin_dashboard.php");
    exit;

}



require_once "config/db.php";




$user_id = $_SESSION["user_id"];

$notification_count = 0;


$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM notifications
     WHERE user_id = ?
     AND is_read = 0"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$notification_count = $row["total"];

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

    <title>Customer Dashboard - PetCare</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>




<nav class="navbar">


   

    <a
        href="index.php"
        class="logo"
    >
        🐾 Pet<span>Care</span>
    </a>


    <ul class="nav-links">


       

        <li>

            <a href="index.php">
                Home
            </a>

        </li>


      

        <li>

            <a href="customer_dashboard.php">
                Dashboard
            </a>

        </li>


   

        <li>

            <a href="shop.php">
                🛍️ Shop
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

            <a href="notifications.php">

                🔔 Messages

                <?php if ($notification_count > 0) { ?>

                    <span
                        style="
                            background:red;
                            color:white;
                            padding:3px 8px;
                            border-radius:20px;
                            font-size:12px;
                            font-weight:800;
                            margin-left:4px;
                        "
                    >

                        <?php
                        echo $notification_count;
                        ?>

                    </span>

                <?php } ?>

            </a>

        </li>



        <li>

            <a href="my_account.php">
                👤 My Account
            </a>

        </li>


      

        <li>

            <a
                href="logout.php"
                class="login-btn"
            >
                LOGOUT
            </a>

        </li>


    </ul>

</nav>





<div class="dashboard">


    <!-- WELCOME SECTION -->

    <div class="dashboard-header">

        <h1>
            Welcome Back! 🐾
        </h1>


        <p>

            Hello,

            <strong>

                <?php

                echo htmlspecialchars(
                    $_SESSION["user_name"]
                );

                ?>

            </strong>

            👋

        </p>


        <p>
            Welcome to your PetCare customer dashboard.
        </p>

    </div>





    <div class="menu">


    

        <a
            href="shop.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
            "
        >

            <div class="icon">
                🛍️
            </div>


            <h3>
                Shop Products
            </h3>


            <p>
                Browse and shop your favourite
                pet products.
            </p>

        </a>



    

        <a
            href="shop.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
            "
        >

            <div class="icon">
                🔍
            </div>


            <h3>
                Search Products
            </h3>


            <p>
                Find products quickly and easily.
            </p>

        </a>



      

        <a
            href="shop.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
            "
        >

            <div class="icon">
                🗂️
            </div>


            <h3>
                Categories
            </h3>


            <p>
                Explore different pet product
                categories.
            </p>

        </a>



        

        <a
            href="cart.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
            "
        >

            <div class="icon">
                🛒
            </div>


            <h3>
                My Cart
            </h3>


            <p>
                View your selected products
                and cart total.
            </p>

        </a>



   

        <a
            href="orders.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
            "
        >

            <div class="icon">
                📦
            </div>


            <h3>
                My Orders
            </h3>


            <p>
                View your order history
                and order status.
            </p>

        </a>



    

        <a
            href="notifications.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
                position:relative;
            "
        >

            <div class="icon">
                🔔
            </div>


            <h3>

                Messages

                <?php if ($notification_count > 0) { ?>

                    <span
                        style="
                            background:red;
                            color:white;
                            padding:4px 9px;
                            border-radius:20px;
                            font-size:12px;
                            font-weight:800;
                            margin-left:5px;
                        "
                    >

                        <?php
                        echo $notification_count;
                        ?>

                    </span>

                <?php } ?>

            </h3>


            <p>

                <?php if ($notification_count > 0) { ?>

                    You have
                    <?php echo $notification_count; ?>
                    new message(s).

                <?php } else { ?>

                    View your order
                    notifications and updates.

                <?php } ?>

            </p>

        </a>



        

        <a
            href="my_account.php"
            class="menu-item"
            style="
                text-decoration:none;
                color:inherit;
            "
        >

            <div class="icon">
                👤
            </div>


            <h3>
                My Account
            </h3>


            <p>
                Manage your PetCare
                customer account.
            </p>

        </a>


    </div>

</div>



</body>

</html>