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




if ($_SERVER["REQUEST_METHOD"] != "POST") {

    header("Location: orders.php");
    exit;

}



$order_id = (int) $_POST["order_id"];
$status = $_POST["status"];




$allowed_statuses = [
    "Pending",
    "Confirmed",
    "Delivered",
    "Cancelled"
];


if (!in_array($status, $allowed_statuses)) {

    header("Location: orders.php");
    exit;

}



$conn->begin_transaction();


try {



    $stmt = $conn->prepare(
        "SELECT
            id,
            user_id,
            status,
            cancelled_by
         FROM orders
         WHERE id = ?
         FOR UPDATE"
    );

    $stmt->bind_param(
        "i",
        $order_id
    );

    $stmt->execute();

    $result = $stmt->get_result();

    $order = $result->fetch_assoc();

    $stmt->close();


  

    if (!$order) {

        throw new Exception(
            "Order not found."
        );

    }


    $user_id =
        $order["user_id"];

    $current_status =
        $order["status"];

    $cancelled_by =
        $order["cancelled_by"];


    

    if (
        $current_status == "Cancelled" &&
        $cancelled_by == "customer"
    ) {

        throw new Exception(
            "This order was cancelled by the customer and cannot be updated."
        );

    }


    

    if (
        $current_status == "Cancelled" &&
        $cancelled_by == "admin"
    ) {

        throw new Exception(
            "This order has already been cancelled."
        );

    }


 

    if ($current_status == $status) {

        throw new Exception(
            "The order already has this status."
        );

    }


   

    if ($status == "Cancelled") {


    

        $stmt = $conn->prepare(
            "SELECT
                product_id,
                quantity
             FROM order_items
             WHERE order_id = ?"
        );

        $stmt->bind_param(
            "i",
            $order_id
        );

        $stmt->execute();

        $items_result =
            $stmt->get_result();


       

        while (
            $item =
            $items_result->fetch_assoc()
        ) {

            $product_id =
                $item["product_id"];

            $quantity =
                $item["quantity"];


            $stock_stmt = $conn->prepare(
                "UPDATE products
                 SET stock = stock + ?
                 WHERE id = ?"
            );

            $stock_stmt->bind_param(
                "ii",
                $quantity,
                $product_id
            );

            $stock_stmt->execute();

            $stock_stmt->close();

        }

        $stmt->close();


     

        $stmt = $conn->prepare(
            "UPDATE orders
             SET status = 'Cancelled',
                 cancelled_by = 'admin'
             WHERE id = ?"
        );

        $stmt->bind_param(
            "i",
            $order_id
        );

        $stmt->execute();

        $stmt->close();


     

        $message =
            "❌ Your order #" .
            $order_id .
            " has been cancelled by the admin.";


    } else {


       

        $stmt = $conn->prepare(
            "UPDATE orders
             SET status = ?,
                 cancelled_by = NULL
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $status,
            $order_id
        );

        $stmt->execute();

        $stmt->close();


       

        if ($status == "Pending") {

            $message =
                "🕒 Your order #" .
                $order_id .
                " is pending.";

        } elseif ($status == "Confirmed") {

            $message =
                "✅ Your order #" .
                $order_id .
                " has been confirmed.";

        } elseif ($status == "Delivered") {

            $message =
                "📦 Your order #" .
                $order_id .
                " has been delivered.";

        }

    }



    $stmt = $conn->prepare(
        "INSERT INTO notifications
        (user_id, order_id, message)
        VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iis",
        $user_id,
        $order_id,
        $message
    );

    $stmt->execute();

    $stmt->close();


   

    $conn->commit();



    header("Location: orders.php");
    exit;


} catch (Exception $e) {


   

    $conn->rollback();


    $_SESSION["cancel_error"] =
        $e->getMessage();


    header("Location: orders.php");
    exit;

}

?>