
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



if (
    !isset($_SESSION["cart"]) ||
    empty($_SESSION["cart"])
) {
    header("Location: cart.php");
    exit;
}


$message = "";
$order_success = false;
$order_id = 0;




$total = 0;

foreach ($_SESSION["cart"] as $item) {

    $total +=
        $item["price"] * $item["quantity"];

}




if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];


    

    $conn->begin_transaction();


    try {

        
        foreach ($_SESSION["cart"] as $item) {

            $product_id = $item["id"];
            $quantity = $item["quantity"];


            $stmt = $conn->prepare(
                "SELECT stock
                 FROM products
                 WHERE id = ?
                 FOR UPDATE"
            );

            $stmt->bind_param(
                "i",
                $product_id
            );

            $stmt->execute();

            $result = $stmt->get_result();


            if ($result->num_rows != 1) {

                throw new Exception(
                    "Product not found."
                );

            }


            $product = $result->fetch_assoc();


            if ($product["stock"] < $quantity) {

                throw new Exception(
                    "Not enough stock available for one or more products."
                );

            }


            $stmt->close();

        }


        

        $stmt = $conn->prepare(
            "INSERT INTO orders
            (user_id, total_amount, payment_method, status)
            VALUES (?, ?, 'Cash on Delivery', 'Pending')"
        );


        $stmt->bind_param(
            "id",
            $user_id,
            $total
        );


        if (!$stmt->execute()) {

            throw new Exception(
                "Failed to create order."
            );

        }


        $order_id = $conn->insert_id;

        $stmt->close();


        

        foreach ($_SESSION["cart"] as $item) {

            $product_id = $item["id"];
            $quantity = $item["quantity"];
            $price = $item["price"];


            $stmt = $conn->prepare(
                "INSERT INTO order_items
                (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)"
            );


            $stmt->bind_param(
                "iiid",
                $order_id,
                $product_id,
                $quantity,
                $price
            );


            if (!$stmt->execute()) {

                throw new Exception(
                    "Failed to save order items."
                );

            }


            $stmt->close();


            

            $stmt = $conn->prepare(
                "UPDATE products
                 SET stock = stock - ?
                 WHERE id = ?
                 AND stock >= ?"
            );


            $stmt->bind_param(
                "iii",
                $quantity,
                $product_id,
                $quantity
            );


            if (!$stmt->execute()) {

                throw new Exception(
                    "Failed to update stock."
                );

            }


            if ($stmt->affected_rows != 1) {

                throw new Exception(
                    "Stock update failed."
                );

            }


            $stmt->close();

        }


        

        $conn->commit();


        

        $_SESSION["cart"] = [];


        $order_success = true;


    } catch (Exception $e) {

      

        $conn->rollback();

        $message = $e->getMessage();

    }

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

    <title>Checkout - PetCare</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>




<nav class="navbar">

    <a
        href="customer_dashboard.php"
        class="logo"
    >
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

        <h1>
            🧾 Checkout
        </h1>

        <p>
            Review your order and place your purchase.
        </p>

    </div>


    <?php if ($order_success): ?>


      

        <div
            class="dashboard-header"
            style="text-align:center;"
        >

            <div
                class="auth-logo"
                style="font-size:60px;"
            >
                ✅
            </div>


            <h2>
                Order Placed Successfully!
            </h2>


            <p>

                Thank you for shopping with PetCare.

            </p>


            <p>

                Your Order ID is:

                <strong>
                    #<?php echo $order_id; ?>
                </strong>

            </p>


            <p>

                Payment Method:

                <strong>
                    Cash on Delivery
                </strong>

            </p>


            <p>

                Total Amount:

                <strong class="price">

                    Rs.
                    <?php
                    echo number_format(
                        $total,
                        2
                    );
                    ?>

                </strong>

            </p>


            <br>


            <a
                href="shop.php"
                class="btn"
            >
                🛍️ Continue Shopping
            </a>


            <a
                href="orders.php"
                class="btn"
                style="margin-left:10px;"
            >
                📦 View My Orders
            </a>

        </div>


    <?php else: ?>


        <?php if (!empty($message)): ?>

            <div class="message">

                <?php
                echo htmlspecialchars(
                    $message
                );
                ?>

            </div>

        <?php endif; ?>


       

        <div class="category-table-wrapper">

            <h2 style="margin-bottom:20px;">
                🛒 Order Summary
            </h2>


            <table class="category-table">

                <thead>

                    <tr>

                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach (
                        $_SESSION["cart"] as $item
                    ): ?>

                        <tr>

                            <td>

                                <?php if (
                                    !empty($item["image"])
                                ): ?>

                                    <img
                                        src="images/<?php
                                        echo htmlspecialchars(
                                            $item["image"]
                                        );
                                        ?>"
                                        alt="<?php
                                        echo htmlspecialchars(
                                            $item["name"]
                                        );
                                        ?>"
                                        style="
                                            width:60px;
                                            height:60px;
                                            object-fit:cover;
                                            border-radius:10px;
                                            vertical-align:middle;
                                            margin-right:10px;
                                        "
                                    >

                                <?php endif; ?>


                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $item["name"]
                                    );
                                    ?>

                                </strong>

                            </td>


                            <td>

                                Rs.
                                <?php
                                echo number_format(
                                    $item["price"],
                                    2
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo $item["quantity"];
                                ?>

                            </td>


                            <td>

                                <strong>

                                    Rs.
                                    <?php
                                    echo number_format(
                                        $item["price"]
                                        * $item["quantity"],
                                        2
                                    );
                                    ?>

                                </strong>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>


    

        <div
            class="dashboard-header"
            style="margin-top:25px;"
        >

            <h2>
                💰 Payment Method
            </h2>


            <div
                style="
                    margin-top:15px;
                    padding:18px;
                    background:#fff1e9;
                    border-radius:15px;
                "
            >

                <strong>
                    🚚 Cash on Delivery
                </strong>

                <p style="margin-top:8px;">

                    Pay when your order is delivered.

                </p>

            </div>


            <div
                style="
                    margin-top:25px;
                    text-align:right;
                "
            >

                <h2>

                    Total:

                    <span class="price">

                        Rs.
                        <?php
                        echo number_format(
                            $total,
                            2
                        );
                        ?>

                    </span>

                </h2>


                <br>


                <a
                    href="cart.php"
                    class="btn"
                >
                    ← Back to Cart
                </a>


                <form
                    method="POST"
                    style="
                        display:inline;
                        margin-left:10px;
                    "
                >

                    <button
                        type="submit"
                        class="btn"
                    >
                        ✅ Place Order
                    </button>

                </form>

            </div>

        </div>


    <?php endif; ?>

</div>


</body>

</html>
