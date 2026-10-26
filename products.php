
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

$result = $conn->query(
    "SELECT
        products.id,
        products.name,
        products.description,
        products.price,
        products.stock,
        products.image,
        categories.name AS category_name
     FROM products
     LEFT JOIN categories
        ON products.category_id = categories.id
     ORDER BY products.id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Product Management - PetCare</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <style>
        .product-message {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 700;
        }

        .product-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .product-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>

</head>

<body>

<nav class="navbar">

    <a
        href="admin_dashboard.php"
        class="logo"
    >

        🐾 Pet<span>Care</span>

    </a>

    <ul class="nav-links">

        <li><a href="admin_dashboard.php">Dashboard</a></li>

        <li><a href="products.php">Products</a></li>

        <li><a href="categories.php">Categories</a></li>

        <li><a href="customers.php">Customers</a></li>

        <li><a href="orders.php">Orders</a></li>

        <li>
            <a href="logout.php" class="login-btn">Logout</a>
        </li>

    </ul>

</nav>

<div class="dashboard">

    <?php if (isset($_GET["deleted"]) && $_GET["deleted"] == "1") { ?>

        <div class="product-message success">
            ✅ Product deleted successfully!
        </div>

    <?php } elseif (isset($_GET["error"]) && $_GET["error"] == "product_in_order") { ?>

        <div class="product-message error">
            ⚠️ This product cannot be deleted because it is already used in an order.
        </div>

    <?php } elseif (isset($_GET["error"]) && $_GET["error"] == "not_found") { ?>

        <div class="product-message error">
            ⚠️ Product not found.
        </div>

    <?php } elseif (isset($_GET["error"])) { ?>

        <div class="product-message error">
            ❌ Unable to delete the product. Please try again.
        </div>

    <?php } ?>

    <div class="dashboard-header">

        <h1>🛍️ Product Management</h1>

        <p>Add, view, edit and delete pet shop products.</p>

    </div>

    <div style="margin-bottom:25px;">

        <a href="add_product.php" class="btn">
            ➕ Add New Product
        </a>

    </div>

    <div class="product-table-wrapper">

        <table class="product-table">

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                <?php if ($result && $result->num_rows > 0) { ?>

                    <?php while ($product = $result->fetch_assoc()) { ?>

                        <tr>

                            <td>
                                <?php echo (int) $product["id"]; ?>
                            </td>

                            <td>

                                <?php if (!empty($product["image"])) {

                                    $product_image = $product["image"];

                                    if (
                                        strpos($product_image, "http://") === 0 ||
                                        strpos($product_image, "https://") === 0
                                    ) {
                                        $product_image_src = $product_image;
                                    } else {
                                        $product_image_src = "images/" . $product_image;
                                    }

                                ?>

                                    <img
                                        src="<?php echo htmlspecialchars($product_image_src, ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="Product Image"
                                    >

                                <?php } else { ?>

                                    <span style="font-size:40px;">🐾</span>

                                <?php } ?>

                            </td>

                            <td>
                                <strong>
                                    <?php echo htmlspecialchars($product["name"], ENT_QUOTES, 'UTF-8'); ?>
                                </strong>
                            </td>

                            <td>

                                <?php if (!empty($product["category_name"])) { ?>

                                    <span
                                        style="
                                            display:inline-block;
                                            padding:7px 12px;
                                            background:#fff1e9;
                                            color:#d94d20;
                                            border-radius:20px;
                                            font-size:13px;
                                            font-weight:800;
                                        "
                                    >
                                        <?php echo htmlspecialchars($product["category_name"], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>

                                <?php } else { ?>

                                    <span style="color:#999;font-weight:700;">
                                        No Category
                                    </span>

                                <?php } ?>

                            </td>

                            <td>

                                <?php

                                $description = $product["description"];

                                if (strlen($description) > 60) {
                                    echo htmlspecialchars(substr($description, 0, 60), ENT_QUOTES, 'UTF-8') . "...";
                                } else {
                                    echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
                                }

                                ?>

                            </td>

                            <td>
                                <strong>
                                    Rs. <?php echo number_format($product["price"], 2); ?>
                                </strong>
                            </td>

                            <td>

                                <?php

                                $stock = (int) $product["stock"];

                                if ($stock == 0) {

                                ?>

                                    <span style="color:#dc3545;font-weight:900;">
                                        ❌ 0
                                    </span>

                                <?php } elseif ($stock <= 5) { ?>

                                    <span style="color:#d99a00;font-weight:900;">
                                        ⚠️ <?php echo $stock; ?>
                                    </span>

                                <?php } else { ?>

                                    <span style="color:#218838;font-weight:900;">
                                        ✅ <?php echo $stock; ?>
                                    </span>

                                <?php } ?>

                            </td>

                            <td>

                                <a
                                    href="edit_product.php?id=<?php echo (int) $product["id"]; ?>"
                                    class="edit-btn"
                                    title="Edit Product"
                                >
                                    ✏️
                                </a>

                                <button
                                    type="button"
                                    class="delete-btn"
                                    title="Delete Product"
                                    onclick='openDeletePopup(
                                        <?php echo (int) $product["id"]; ?>,
                                        <?php echo json_encode(
                                            $product["name"],
                                            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
                                        ); ?>
                                    )'
                                >
                                    🗑️ Delete
                                </button>

                            </td>

                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>

                        <td
                            colspan="8"
                            style="
                                text-align:center;
                                padding:40px;
                                font-weight:700;
                                color:#777;
                            "
                        >
                            🐾 No products found.
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    </div>

</div>


<!-- Delete Product Popup -->

<div
    class="popup-overlay"
    id="deletePopup"
>

    <div class="delete-popup">

        <div class="delete-icon">
            🗑️
        </div>

        <h2>
            Delete Product?
        </h2>

        <p>
            Are you sure you want to delete
            <strong id="productName"></strong>?
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
                action="delete_product.php"
            >

                <input
                    type="hidden"
                    name="id"
                    id="deleteProductId"
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
        "deleteProductId"
    ).value = id;

    document.getElementById(
        "productName"
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

