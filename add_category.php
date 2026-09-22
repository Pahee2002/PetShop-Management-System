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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);

    if (empty($name)) {

        $message = "Please enter a category name.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO categories (name, description)
             VALUES (?, ?)"
        );

        $stmt->bind_param(
            "ss",
            $name,
            $description
        );

        if ($stmt->execute()) {

            $message = "Category added successfully!";

        } else {

            if ($conn->errno == 1062) {
                $message = "This category already exists.";
            } else {
                $message = "Failed to add category.";
            }

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Category - PetCare</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<nav class="navbar">

    <a href="admin_dashboard.php" class="logo">
        🐾 Pet<span>Care</span>
    </a>

    <ul class="nav-links">

        <li>
            <a href="admin_dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="logout.php" class="login-btn">
                LOGOUT
            </a>
        </li>

    </ul>

</nav>


<div class="auth-page">

    <div class="auth-card">

        <div class="auth-logo">
            🗂️
        </div>

        <h2>Add New Category</h2>

        <p>
            Create a new category for your PetCare shop.
        </p>


        <?php if (!empty($message)): ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <div class="form-group">

                <label for="name">
                    Category Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="Example: Dog Products"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Enter category description"
                    rows="4"
                    style="width:100%; padding:14px; border:2px solid #eeeeee; border-radius:12px; font-family:inherit; resize:vertical;"
                ></textarea>

            </div>


            <button
                type="submit"
                class="btn"
            >
                ADD CATEGORY
            </button>

        </form>


        <p style="margin-top:25px;">

            <a href="admin_dashboard.php">
                ← Back to Dashboard
            </a>

        </p>

    </div>

</div>

</body>

</html>