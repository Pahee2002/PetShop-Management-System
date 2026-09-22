
<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["user_role"] != "admin") {
    header("Location: customer_dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard - PetCare</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>




<nav class="navbar">

    <a href="index.php" class="logo">
        🐾 Pet<span>Care</span>
    </a>

    <ul class="nav-links">

        <li>
            <a href="index.php">
                Home
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
            Admin Dashboard 👋
        </h1>

        <p>
            Welcome,
            <strong>
                <?php
                echo htmlspecialchars(
                    $_SESSION["user_name"]
                );
                ?>
            </strong>
        </p>

        <p>
            Manage your PetCare shop from here.
        </p>

    </div>



  

    <div class="menu">


        <!-- Product Management -->

        <a
            href="products.php"
            class="menu-item"
            style="text-decoration: none; color: inherit;"
        >

            <div class="icon">
                🐾
            </div>

            <h3>
                Product Management
            </h3>

            <p>
                Add, edit, delete and manage pet products.
            </p>

        </a>



      

        <a
            href="categories.php"
            class="menu-item"
            style="text-decoration: none; color: inherit;"
        >

            <div class="icon">
                🗂️
            </div>

            <h3>
                Category Management
            </h3>

            <p>
                Manage product categories.
            </p>

        </a>



       

        <a
            href="customers.php"
            class="menu-item"
            style="text-decoration: none; color: inherit;"
        >

            <div class="icon">
                👥
            </div>

            <h3>
                Customer Management
            </h3>

            <p>
                View and manage customer accounts.
            </p>

        </a>



      

        <a
            href="orders.php"
            class="menu-item"
            style="text-decoration: none; color: inherit;"
        >

            <div class="icon">
                🛒
            </div>

            <h3>
                Order Management
            </h3>

            <p>
                View and manage customer orders.
            </p>

        </a>



     

        <a
            href="stock.php"
            class="menu-item"
            style="text-decoration: none; color: inherit;"
        >

            <div class="icon">
                📦
            </div>

            <h3>
                Stock Management
            </h3>

            <p>
                Monitor and update product stock.
            </p>

        </a>



   

        <a
            href="reports.php"
            class="menu-item"
            style="text-decoration: none; color: inherit;"
        >

            <div class="icon">
                📊
            </div>

            <h3>
                Reports
            </h3>

            <p>
                View sales and shop reports.
            </p>

        </a>


    </div>

</div>


</body>

</html>

