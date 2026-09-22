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
    header("Location: products.php");
    exit;
}

if (!isset($_POST["id"])) {
    header("Location: products.php");
    exit;
}

$id = (int) $_POST["id"];

$stmt = $conn->prepare(
    "DELETE FROM products WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->close();

header("Location: products.php?deleted=1");
exit;

?>