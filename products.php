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

        <li>
            <a href="admin_dashboard.php">
                Dashboard
            </a>
        </li>

        <li>
            <a href="products.php">
                Products
            </a>
        </li>

        <li>
            <a href="categories.php">
                Categories
            </a>
        </li>

        <li>
            <a href="customers.php">
                Customers
            </a>
        </li>

        <li>
            <a href="orders.php">
                Orders
            </a>
        </li>

        <li>
            <a
                href="logout.php"
                class="login-btn"
            >
                Logout
            </a>
        </li>

    </ul>

</nav>





<div class="dashboard">


    <div class="dashboard-header">

        <h1>
            🛍️ Product Management
        </h1>

        <p>
            Add, view, edit and delete pet shop products.
        </p>

    </div>





    <div style="margin-bottom:25px;">

        <a
            href="add_product.php"
            class="btn"
        >
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


                <?php if ($result->num_rows > 0) { ?>


                    <?php while (
                        $product =
                        $result->fetch_assoc()
                    ) { ?>


                        <tr>


                          

                            <td>

                                <?php
                                echo $product["id"];
                                ?>

                            </td>



                          

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $product["image"]
                                    )
                                ) {


                                    $product_image =
                                        $product["image"];


                                    

                                    if (
                                        strpos(
                                            $product_image,
                                            "http://"
                                        ) === 0 ||
                                        strpos(
                                            $product_image,
                                            "https://"
                                        ) === 0
                                    ) {

                                        

                                        $product_image_src =
                                            $product_image;

                                    } else {

                                       

                                        $product_image_src =
                                            "images/" .
                                            $product_image;

                                    }

                                ?>

                                    <img
                                        src="<?php
                                        echo htmlspecialchars(
                                            $product_image_src
                                        );
                                        ?>"
                                        alt="Product Image"
                                    >

                                <?php

                                } else {

                                ?>

                                    <span
                                        style="
                                            font-size:40px;
                                        "
                                    >
                                        🐾
                                    </span>

                                <?php

                                }

                                ?>

                            </td>



                           

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $product["name"]
                                    );
                                    ?>

                                </strong>

                            </td>



                          

                            <td>

                                <?php

                                if (
                                    !empty(
                                        $product["category_name"]
                                    )
                                ) {

                                ?>

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

                                        <?php
                                        echo htmlspecialchars(
                                            $product[
                                                "category_name"
                                            ]
                                        );
                                        ?>

                                    </span>

                                <?php

                                } else {

                                ?>

                                    <span
                                        style="
                                            color:#999;
                                            font-weight:700;
                                        "
                                    >

                                        No Category

                                    </span>

                                <?php

                                }

                                ?>

                            </td>



                         

                            <td>

                                <?php

                                $description =
                                    $product[
                                        "description"
                                    ];


                                if (
                                    strlen(
                                        $description
                                    ) > 60
                                ) {

                                    echo htmlspecialchars(
                                        substr(
                                            $description,
                                            0,
                                            60
                                        )
                                    ) . "...";

                                } else {

                                    echo htmlspecialchars(
                                        $description
                                    );

                                }

                                ?>

                            </td>



                          

                            <td>

                                <strong>

                                    Rs.

                                    <?php

                                    echo number_format(
                                        $product["price"],
                                        2
                                    );

                                    ?>

                                </strong>

                            </td>



                           

                            <td>

                                <?php

                                $stock =
                                    (int) $product["stock"];


                               

                                if ($stock == 0) {

                                ?>

                                    <span
                                        style="
                                            color:#dc3545;
                                            font-weight:900;
                                        "
                                    >

                                        ❌ 0

                                    </span>

                                <?php



                                } elseif ($stock <= 5) {

                                ?>

                                    <span
                                        style="
                                            color:#d99a00;
                                            font-weight:900;
                                        "
                                    >

                                        ⚠️

                                        <?php
                                        echo $stock;
                                        ?>

                                    </span>

                                <?php


                                

                                } else {

                                ?>

                                    <span
                                        style="
                                            color:#218838;
                                            font-weight:900;
                                        "
                                    >

                                        ✅

                                        <?php
                                        echo $stock;
                                        ?>

                                    </span>

                                <?php

                                }

                                ?>

                            </td>



                         

                            <td>


                              

                                <a
                                    href="edit_product.php?id=<?php
                                    echo $product["id"];
                                    ?>"
                                    class="edit-btn"
                                    title="Edit Product"
                                >

                                    ✏️

                                </a>



                           

                                <a
                                    href="delete_product.php?id=<?php
                                    echo $product["id"];
                                    ?>"
                                    class="delete-btn"
                                    onclick="
                                        return confirm(
                                            'Are you sure you want to delete this product?'
                                        );
                                    "
                                >

                                    Delete

                                </a>


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


</body>

</html>