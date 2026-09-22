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

$result = $conn->query(
    "SELECT * FROM categories ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Categories - PetCare</title>

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


<div class="dashboard">

    <div class="dashboard-header">

        <h1>🗂️ Category Management</h1>

        <p>
            View and manage all categories in your PetCare shop.
        </p>

    </div>


    <a href="add_category.php" class="btn add-btn">
        ➕ Add New Category
    </a>


    <div class="category-table-wrapper">

        <?php if ($result->num_rows > 0): ?>

            <table class="category-table">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Created Date</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                    <?php while ($category = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $category["id"]; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $category["name"]
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $category["description"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $category["created_at"]
                                    )
                                );
                                ?>
                            </td>

                            <td>

                                <a
                                    href="edit_category.php?id=<?php echo $category["id"]; ?>"
                                    class="edit-btn"
                                >
                                    ✏️ Edit
                                </a>

                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="openDeletePopup(
                                        <?php echo $category["id"]; ?>,
                                        '<?php echo htmlspecialchars(
                                            addslashes($category["name"])
                                        ); ?>'
                                    )"
                                >
                                    🗑️ Delete
                                </button>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty-message">

                <h3>🐾 No Categories Yet</h3>

                <p>
                    Add your first category to the shop.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>




<div
    class="popup-overlay"
    id="deletePopup"
>

    <div class="delete-popup">

        <div class="delete-icon">
            🗑️
        </div>

        <h2>
            Delete Category?
        </h2>

        <p>

            Are you sure you want to delete

            <strong id="categoryName"></strong>?

            <br>

            This action cannot be undone.

        </p>


        <div class="popup-buttons">

            <button
                type="button"
                class="cancel-btn"
                onclick="closeDeletePopup()"
            >
                Cancel
            </button>


            <form
                method="POST"
                action="delete_category.php"
            >

                <input
                    type="hidden"
                    name="id"
                    id="deleteCategoryId"
                >

                <button
                    type="submit"
                    class="confirm-delete-btn"
                >
                    Yes, Delete
                </button>

            </form>

        </div>

    </div>

</div>


<script>

function openDeletePopup(id, name) {

    document.getElementById(
        "deleteCategoryId"
    ).value = id;

    document.getElementById(
        "categoryName"
    ).textContent = name;

    document.getElementById(
        "deletePopup"
    ).classList.add("show");

}


function closeDeletePopup() {

    document.getElementById(
        "deletePopup"
    ).classList.remove("show");

}


document.getElementById(
    "deletePopup"
).addEventListener(
    "click",
    function(event) {

        if (event.target === this) {
            closeDeletePopup();
        }

    }
);

</script>


</body>

</html>