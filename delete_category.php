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
    header("Location: categories.php");
    exit;
}

if (!isset($_POST["id"])) {
    header("Location: categories.php");
    exit;
}

$id = (int) $_POST["id"];

$stmt = $conn->prepare(
    "DELETE FROM categories WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$stmt->close();

header("Location: categories.php");
exit;

?>