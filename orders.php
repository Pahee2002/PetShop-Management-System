<?php

session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["user_role"];




$cancel_error = "";

if (isset($_SESSION["cancel_error"])) {

    $cancel_error = $_SESSION["cancel_error"];

    unset($_SESSION["cancel_error"]);
}



if ($role == "admin") {

    $sql = "SELECT orders.*, users.name AS customer_name
            FROM orders
            INNER JOIN users ON orders.user_id = users.id
            ORDER BY orders.id DESC";

    $result = $conn->query($sql);

} else {

    $stmt = $conn->prepare(
        "SELECT *
         FROM orders
         WHERE user_id = ?
         ORDER BY id DESC"
    );

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $result = $stmt->get_result();
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

    <title>
        <?php
        echo ($role == "admin")
            ? "Order Management - PetCare"
            : "My Orders - PetCare";
        ?>
    </title>

    <link rel="stylesheet" href="css/style.css">


    <!-- Cancel Popup CSS -->

    <style>

        .cancel-popup-overlay {

            display: none;

            position: fixed;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.55);

            z-index: 9999;

            align-items: center;
            justify-content: center;

        }


        .cancel-popup {

            width: 90%;
            max-width: 450px;

            background: white;

            border-radius: 20px;

            padding: 35px;

            text-align: center;

            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.25);

            animation: popupShow 0.25s ease;

        }


        @keyframes popupShow {

            from {

                transform: scale(0.8);

                opacity: 0;

            }

            to {

                transform: scale(1);

                opacity: 1;

            }

        }


        .cancel-popup-icon {

            font-size: 55px;

            margin-bottom: 15px;

        }


        .cancel-popup h2 {

            color: #333;

            margin-bottom: 12px;

            font-size: 24px;

        }


        .cancel-popup p {

            color: #777;

            line-height: 1.6;

            margin-bottom: 25px;

        }


        .cancel-popup-buttons {

            display: flex;

            gap: 12px;

            justify-content: center;

        }


        .popup-keep-btn {

            background: #eeeeee;

            color: #333;

            border: none;

            padding: 12px 22px;

            border-radius: 10px;

            font-weight: 800;

            cursor: pointer;

        }


        .popup-cancel-btn {

            background: #ff4d4d;

            color: white;

            border: none;

            padding: 12px 22px;

            border-radius: 10px;

            font-weight: 800;

            cursor: pointer;

        }


        .popup-keep-btn:hover {

            background: #dddddd;

        }


        .popup-cancel-btn:hover {

            background: #e63e3e;

        }

    </style>

</head>


<body>




<nav class="navbar">

    <?php if ($role == "admin") { ?>

        <a href="admin_dashboard.php" class="logo">
            🐾 Pet<span>Care</span>
        </a>

    <?php } else { ?>

        <a href="customer_dashboard.php" class="logo">
            🐾 Pet<span>Care</span>
        </a>

    <?php } ?>


    <ul class="nav-links">


        <?php if ($role == "admin") { ?>


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


        <?php } else { ?>


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
                <a href="my_account.php">
                    👤 My Account
                </a>
            </li>


        <?php } ?>


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


    <div class="dashboard-header">

        <?php if ($role == "admin") { ?>

            <h1>
                Order Management
            </h1>

            <p>
                View and manage customer orders.
            </p>

        <?php } else { ?>

            <h1>
                My Orders 📦
            </h1>

            <p>
                View your order history and order status.
            </p>

        <?php } ?>

    </div>



   

    <?php if ($cancel_error != "") { ?>

        <div class="message">

            ❌
            <?php
            echo htmlspecialchars($cancel_error);
            ?>

        </div>

    <?php } ?>



    <div class="order-table-wrapper">


        <?php if ($result && $result->num_rows > 0) { ?>


            <table class="order-table">


                <thead>

                    <tr>

                        <th>
                            Order ID
                        </th>


                        <?php if ($role == "admin") { ?>

                            <th>
                                Customer
                            </th>

                        <?php } ?>


                        <th>
                            Total Amount
                        </th>


                        <th>
                            Payment
                        </th>


                        <th>
                            Status
                        </th>


                        <th>
                            Order Date
                        </th>


                        <th>
                            Action
                        </th>

                    </tr>

                </thead>



                <tbody>


                    <?php while ($order = $result->fetch_assoc()) { ?>


                        <?php

                        $status = $order["status"];


                        if ($status == "Pending") {

                            $status_class = "status-pending";
                            $status_icon = "⏳";

                        } elseif ($status == "Confirmed") {

                            $status_class = "status-confirmed";
                            $status_icon = "✅";

                        } elseif ($status == "Delivered") {

                            $status_class = "status-delivered";
                            $status_icon = "📦";

                        } else {

                            $status_class = "status-cancelled";
                            $status_icon = "❌";

                        }


                      

                        $can_cancel = false;


                        if (
                            $role == "customer"
                            &&
                            $status == "Pending"
                        ) {

                            $created_time =
                                strtotime(
                                    $order["created_at"]
                                );

                            $current_time =
                                time();

                            $difference =
                                $current_time -
                                $created_time;


                            if (
                                $difference
                                <=
                                2 * 60 * 60
                            ) {

                                $can_cancel = true;

                            }

                        }

                        ?>


                        <tr>


                           

                            <td>

                                <strong class="order-id-text">

                                    #
                                    <?php
                                    echo $order["id"];
                                    ?>

                                </strong>

                            </td>



                    

                            <?php if ($role == "admin") { ?>

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $order["customer_name"]
                                    );
                                    ?>

                                </td>

                            <?php } ?>



                        

                            <td>

                                <strong>

                                    Rs.
                                    <?php
                                    echo number_format(
                                        $order["total_amount"],
                                        2
                                    );
                                    ?>

                                </strong>

                            </td>



                       

                            <td>

                                <span class="payment-badge">

                                    <?php
                                    echo htmlspecialchars(
                                        $order["payment_method"]
                                    );
                                    ?>

                                </span>

                            </td>



                     
                            <td>


                                <?php if ($role == "admin") { ?>


                                    <form
                                        method="POST"
                                        action="update_order_status.php"
                                        class="status-form"
                                    >


                                        <input
                                            type="hidden"
                                            name="order_id"
                                            value="<?php echo $order["id"]; ?>"
                                        >


                                        <select
                                            name="status"
                                            class="status-select <?php echo $status_class; ?>"
                                            onchange="this.form.submit()"
                                        >


                                            <option
                                                value="Pending"
                                                <?php
                                                echo ($status == "Pending")
                                                    ? "selected"
                                                    : "";
                                                ?>
                                            >
                                                ⏳ Pending
                                            </option>


                                            <option
                                                value="Confirmed"
                                                <?php
                                                echo ($status == "Confirmed")
                                                    ? "selected"
                                                    : "";
                                                ?>
                                            >
                                                ✅ Confirmed
                                            </option>


                                            <option
                                                value="Delivered"
                                                <?php
                                                echo ($status == "Delivered")
                                                    ? "selected"
                                                    : "";
                                                ?>
                                            >
                                                📦 Delivered
                                            </option>


                                            <option
                                                value="Cancelled"
                                                <?php
                                                echo ($status == "Cancelled")
                                                    ? "selected"
                                                    : "";
                                                ?>
                                            >
                                                ❌ Cancelled
                                            </option>


                                        </select>


                                    </form>


                                <?php } else { ?>


                                    <span
                                        class="status-badge <?php echo $status_class; ?>"
                                    >

                                        <?php
                                        echo $status_icon;
                                        ?>

                                        <?php
                                        echo htmlspecialchars(
                                            $status
                                        );
                                        ?>

                                    </span>


                                <?php } ?>


                            </td>



                           

                            <td>

                                <?php

                                echo date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $order["created_at"]
                                    )
                                );

                                ?>

                            </td>




                            <td>


                                <?php if ($role == "customer") { ?>


                                    <?php if ($can_cancel) { ?>


                                        <button
                                            type="button"
                                            class="popup-cancel-btn"
                                            onclick="openCancelPopup(<?php echo $order["id"]; ?>)"
                                        >
                                            ❌ Cancel Order
                                        </button>


                                    <?php } elseif ($status == "Pending") { ?>


                                        <span
                                            style="
                                                color:#999;
                                                font-weight:700;
                                                white-space:nowrap;
                                            "
                                        >
                                            ⏰ Cancellation Expired
                                        </span>


                                    <?php } else { ?>


                                        <span
                                            style="
                                                color:#999;
                                                font-weight:700;
                                            "
                                        >
                                            Not Available
                                        </span>


                                    <?php } ?>


                                <?php } else { ?>


                                    <span
                                        style="
                                            color:#777;
                                            font-weight:600;
                                        "
                                    >
                                        Manage Status
                                    </span>


                                <?php } ?>


                            </td>


                        </tr>


                    <?php } ?>


                </tbody>


            </table>


        <?php } else { ?>


            <div class="no-orders">


                <h2>
                    No Orders Found
                </h2>


                <p>

                    <?php

                    if ($role == "admin") {

                        echo "There are no customer orders yet.";

                    } else {

                        echo "You have not placed any orders yet.";

                    }

                    ?>

                </p>


                <?php if ($role == "customer") { ?>


                    <br>


                    <a
                        href="shop.php"
                        class="btn"
                    >
                        🛒 Start Shopping
                    </a>


                <?php } ?>


            </div>


        <?php } ?>


    </div>


</div>




<div
    id="cancelPopup"
    class="cancel-popup-overlay"
>


    <div class="cancel-popup">


        <div class="cancel-popup-icon">
            ⚠️
        </div>


        <h2>
            Cancel Order?
        </h2>


        <p>
            Are you sure you want to cancel this order?
            Your purchased product quantity will be returned
            to the available stock.
        </p>


        <div class="cancel-popup-buttons">


            <button
                type="button"
                class="popup-keep-btn"
                onclick="closeCancelPopup()"
            >
                Keep Order
            </button>


            <button
                type="button"
                class="popup-cancel-btn"
                onclick="confirmCancelOrder()"
            >
                Yes, Cancel Order
            </button>


        </div>


    </div>

</div>





<script>

    let selectedOrderId = null;


    function openCancelPopup(orderId) {

        selectedOrderId = orderId;

        document.getElementById(
            "cancelPopup"
        ).style.display = "flex";

    }


    function closeCancelPopup() {

        selectedOrderId = null;

        document.getElementById(
            "cancelPopup"
        ).style.display = "none";

    }


    function confirmCancelOrder() {

        if (selectedOrderId !== null) {

            window.location.href =
                "cancel_order.php?id="
                + selectedOrderId;

        }

    }


   

    document.getElementById(
        "cancelPopup"
    ).addEventListener(
        "click",
        function(event) {

            if (
                event.target === this
            ) {

                closeCancelPopup();

            }

        }
    );

</script>


</body>

</html>