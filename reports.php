<?php

session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["user_role"] != "admin") {
    header("Location: customer_dashboard.php");
    exit;
}



$order_query = $conn->query(
    "SELECT COUNT(*) AS total_orders
     FROM orders"
);

$order_data = $order_query->fetch_assoc();

$total_orders = $order_data["total_orders"];




$customer_query = $conn->query(
    "SELECT COUNT(*) AS total_customers
     FROM users
     WHERE role = 'customer'"
);

$customer_data = $customer_query->fetch_assoc();

$total_customers = $customer_data["total_customers"];




$product_query = $conn->query(
    "SELECT COUNT(*) AS total_products
     FROM products"
);

$product_data = $product_query->fetch_assoc();

$total_products = $product_data["total_products"];




$stock_query = $conn->query(
    "SELECT COALESCE(SUM(stock), 0) AS total_stock
     FROM products"
);

$stock_data = $stock_query->fetch_assoc();

$total_stock = $stock_data["total_stock"];




$revenue_query = $conn->query(
    "SELECT COALESCE(SUM(total_amount), 0) AS total_revenue
     FROM orders
     WHERE status != 'Cancelled'"
);

$revenue_data = $revenue_query->fetch_assoc();

$total_revenue = $revenue_data["total_revenue"];




$pending_query = $conn->query(
    "SELECT COUNT(*) AS pending_orders
     FROM orders
     WHERE status = 'Pending'"
);

$pending_data = $pending_query->fetch_assoc();

$pending_orders = $pending_data["pending_orders"];




$confirmed_query = $conn->query(
    "SELECT COUNT(*) AS confirmed_orders
     FROM orders
     WHERE status = 'Confirmed'"
);

$confirmed_data = $confirmed_query->fetch_assoc();

$confirmed_orders = $confirmed_data["confirmed_orders"];




$delivered_query = $conn->query(
    "SELECT COUNT(*) AS delivered_orders
     FROM orders
     WHERE status = 'Delivered'"
);

$delivered_data = $delivered_query->fetch_assoc();

$delivered_orders = $delivered_data["delivered_orders"];




$cancelled_query = $conn->query(
    "SELECT COUNT(*) AS cancelled_orders
     FROM orders
     WHERE status = 'Cancelled'"
);

$cancelled_data = $cancelled_query->fetch_assoc();

$cancelled_orders = $cancelled_data["cancelled_orders"];




$low_stock_query = $conn->query(
    "SELECT COUNT(*) AS low_stock_products
     FROM products
     WHERE stock > 0
     AND stock <= 5"
);

$low_stock_data = $low_stock_query->fetch_assoc();

$low_stock_products = $low_stock_data["low_stock_products"];




$out_stock_query = $conn->query(
    "SELECT COUNT(*) AS out_stock_products
     FROM products
     WHERE stock = 0"
);

$out_stock_data = $out_stock_query->fetch_assoc();

$out_stock_products = $out_stock_data["out_stock_products"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reports - PetCare</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<nav class="navbar">

    <a
        href="admin_dashboard.php"
        class="logo"
    >
        Pet<span>Care</span>
    </a>


    <ul class="nav-links">

        <li>
            <a href="admin_dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="products.php">
                Products
            </a>
        </li>

        <li>
            <a href="categories.php">
                Categories
            </a>
        </li>

        <li>
            <a href="customers.php">
                Customers
            </a>
        </li>

        <li>
            <a href="orders.php">
                Orders
            </a>
        </li>

        <li>
            <a
                href="logout.php"
                class="login-btn"
            >
                Logout
            </a>
        </li>

    </ul>

</nav>



<div class="dashboard">


    <div class="dashboard-header">

        <h1>
            Reports
        </h1>

        <p>
            View overall sales, order, customer and stock information.
        </p>

    </div>



   

    <div class="menu">


        <div class="menu-item">

            <h2>
                📦
            </h2>

            <h3>
                Total Orders
            </h3>

            <p>
                <?php echo $total_orders; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                👥
            </h2>

            <h3>
                Total Customers
            </h3>

            <p>
                <?php echo $total_customers; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                🐾
            </h2>

            <h3>
                Total Products
            </h3>

            <p>
                <?php echo $total_products; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                📦
            </h2>

            <h3>
                Current Stock
            </h3>

            <p>
                <?php echo $total_stock; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                💰
            </h2>

            <h3>
                Total Revenue
            </h3>

            <p>
                Rs. <?php echo number_format($total_revenue, 2); ?>
            </p>

        </div>


    </div>



   

    <div class="dashboard-header">

        <h2>
            Order Status Summary
        </h2>

    </div>


    <div class="menu">


        <div class="menu-item">

            <h2>
                ⏳
            </h2>

            <h3>
                Pending
            </h3>

            <p>
                <?php echo $pending_orders; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                ✅
            </h2>

            <h3>
                Confirmed
            </h3>

            <p>
                <?php echo $confirmed_orders; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                📦
            </h2>

            <h3>
                Delivered
            </h3>

            <p>
                <?php echo $delivered_orders; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                ❌
            </h2>

            <h3>
                Cancelled
            </h3>

            <p>
                <?php echo $cancelled_orders; ?>
            </p>

        </div>


    </div>



   

    <div class="dashboard-header">

        <h2>
            Stock Summary
        </h2>

    </div>


    <div class="menu">


        <div class="menu-item">

            <h2>
                ⚠️
            </h2>

            <h3>
                Low Stock Products
            </h3>

            <p>
                <?php echo $low_stock_products; ?>
            </p>

        </div>



        <div class="menu-item">

            <h2>
                ❌
            </h2>

            <h3>
                Out of Stock
            </h3>

            <p>
                <?php echo $out_stock_products; ?>
            </p>

        </div>


    </div>


</div>


</body>

</html>