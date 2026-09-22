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

if (!isset($_GET["id"])) {
    header("Location: categories.php");
    exit;
}

$id = (int) $_GET["id"];

$stmt = $conn->prepare(
    "SELECT * FROM categories WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: categories.php");
    exit;
}

$category = $result->fetch_assoc();

$stmt->close();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);

    if (empty($name)) {

        $message = "Please enter a category name.";

    } else {

        $update = $conn->prepare(
            "UPDATE categories
             SET name = ?, description = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "ssi",
            $name,
            $description,
            $id
        );

        if ($update->execute()) {

            header("Location: categories.php");
            exit;

        } else {

            if ($conn->errno == 1062) {
                $message = "This category already exists.";
            } else {
                $message = "Failed to update category.";
            }

        }

        $update->close();
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

    <title>Edit Category - PetCare</title>

    <link rel="stylesheet" href="css/style.css">

</head>

<body>


<nav class="navbar">

    <a href="admin_dashboard.php" class="logo">
        🐾 Pet<span>Care</span>
    </a>

    <ul class="nav-links">

        <li>
            <a href="categories.php">
                Categories
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
            ✏️
        </div>


        <h2>
            Edit Category
        </h2>


        <p>
            Update your category information.
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
                    value="<?php echo htmlspecialchars(
                        $category["name"]
                    ); ?>"
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
                    rows="4"
                    style="width:100%; padding:14px; border:2px solid #eeeeee; border-radius:12px; font-family:inherit; resize:vertical;"
                ><?php echo htmlspecialchars(
                    $category["description"]
                ); ?></textarea>

            </div>


            <button
                type="submit"
                class="btn"
            >
                UPDATE CATEGORY
            </button>


        </form>


        <p style="margin-top:25px;">

            <a href="categories.php">
                ← Back to Categories
            </a>

        </p>


    </div>

</div>


</body>

</html>