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




if (!isset($_GET["id"])) {

    header("Location: orders.php");
    exit;

}

$order_id = (int) $_GET["id"];

$user_id = $_SESSION["user_id"];




$conn->begin_transaction();


try {


  

    $stmt = $conn->prepare(
        "SELECT
            id,
            user_id,
            status,
            cancelled_by,
            created_at
         FROM orders
         WHERE id = ?
         AND user_id = ?
         FOR UPDATE"
    );

    $stmt->bind_param(
        "ii",
        $order_id,
        $user_id
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


   

    if ($order["status"] != "Pending") {

        throw new Exception(
            "This order cannot be cancelled."
        );

    }


  

    $created_time =
        strtotime($order["created_at"]);

    $current_time =
        time();

    $difference =
        $current_time - $created_time;


    if ($difference > 2 * 60 * 60) {

        throw new Exception(
            "The 2-hour cancellation period has expired."
        );

    }


    

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
             cancelled_by = 'customer'
         WHERE id = ?
         AND user_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $order_id,
        $user_id
    );

    $stmt->execute();

    $stmt->close();


    

    $message =
        "❌ Your order #" .
        $order_id .
        " has been cancelled successfully.";


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