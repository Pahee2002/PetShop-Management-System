
<?php

session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] != "admin") {
    header("Location: customer_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: products.php");
    exit;
}

if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
    header("Location: products.php?error=invalid_id");
    exit;
}

$id = (int) $_POST["id"];

// Check whether the product exists
$check_stmt = $conn->prepare(
    "SELECT id FROM products WHERE id = ?"
);

$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    $check_stmt->close();
    header("Location: products.php?error=not_found");
    exit;
}

$check_stmt->close();

// Check whether the product is used in an order
$order_stmt = $conn->prepare(
    "SELECT COUNT(*) FROM order_items WHERE product_id = ?"
);

$order_stmt->bind_param("i", $id);
$order_stmt->execute();
$order_stmt->bind_result($order_count);
$order_stmt->fetch();
$order_stmt->close();

if ($order_count > 0) {
    header("Location: products.php?error=product_in_order");
    exit;
}

// Delete the product if it is not used in any order
$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ?"
);

$stmt->bind_param("i", $id);

try {

    if ($stmt->execute() && $stmt->affected_rows > 0) {

        $stmt->close();

        header("Location: products.php?deleted=1");
        exit;

    } else {

        $stmt->close();

        header("Location: products.php?error=delete_failed");
        exit;

    }

} catch (mysqli_sql_exception $e) {

    $stmt->close();

    header("Location: products.php?error=delete_failed");
    exit;

}

?>
