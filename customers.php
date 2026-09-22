
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
    "SELECT id, name, email, created_at
     FROM users
     WHERE role = 'customer'
     ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Management - PetCare</title>

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

        <h1>👥 Customer Management</h1>

        <p>
            View and manage registered PetCare customers.
        </p>

    </div>


    <div class="customer-table-wrapper">

        <?php if ($result->num_rows > 0): ?>

            <table class="customer-table">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Email Address</th>
                        <th>Registered Date</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    <?php while ($customer = $result->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <?php echo $customer["id"]; ?>
                            </td>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $customer["name"]
                                    );
                                    ?>
                                </strong>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $customer["email"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime(
                                        $customer["created_at"]
                                    )
                                );
                                ?>
                            </td>

                            <td>

                                <!-- DELETE BUTTON -->

                                <button
                                    type="button"
                                    class="delete-btn"
                                    onclick="openDeletePopup(
                                        <?php echo $customer["id"]; ?>,
                                        '<?php echo htmlspecialchars(
                                            addslashes($customer["name"])
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

                <h3>👥 No Customers Yet</h3>

                <p>
                    No customers have registered yet.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>




<div class="popup-overlay" id="deletePopup">

    <div class="delete-popup">

        <div class="delete-icon">
            🗑️
        </div>

        <h2>
            Delete Customer?
        </h2>

        <p>
            Are you sure you want to delete
            <strong id="customerName"></strong>?
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
                action="delete_customer.php"
            >

                <input
                    type="hidden"
                    name="id"
                    id="deleteCustomerId"
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

    document.getElementById("deleteCustomerId").value = id;

    document.getElementById("customerName").textContent = name;

    document.getElementById("deletePopup").classList.add("show");

}


function closeDeletePopup() {

    document.getElementById("deletePopup").classList.remove("show");

}


document.getElementById("deletePopup").addEventListener(
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

