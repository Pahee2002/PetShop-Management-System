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




$category_id = 0;

if (isset($_GET["category"])) {
    $category_id = (int) $_GET["category"];
}




$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}




$category_result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);




$sql = "SELECT
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
        WHERE 1=1";




if ($category_id > 0) {
    $sql .= " AND products.category_id = ?";
}




if (!empty($search)) {
    $sql .= " AND products.name LIKE ?";
}


$sql .= " ORDER BY products.id DESC";




$stmt = $conn->prepare($sql);




if ($category_id > 0 && !empty($search)) {

    $search_value = "%" . $search . "%";

    $stmt->bind_param(
        "is",
        $category_id,
        $search_value
    );

} elseif ($category_id > 0) {

    $stmt->bind_param(
        "i",
        $category_id
    );

} elseif (!empty($search)) {

    $search_value = "%" . $search . "%";

    $stmt->bind_param(
        "s",
        $search_value
    );
}




$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Shop - PetCare</title>

<link
    rel="stylesheet"
    href="css/style.css"
>


</head>

<body>



<nav class="navbar">

<a
    href="customer_dashboard.php"
    class="logo"
>
    🐾 Pet<span>Care</span>
</a>


<ul class="nav-links">

    <li>
        <a href="customer_dashboard.php">
            Dashboard
        </a>
    </li>

    <li>
        <a href="shop.php">
            Shop
        </a>
    </li>

    <li>
        <a href="cart.php">
            🛒 Cart
        </a>
    </li>

    <li>
        <a href="orders.php">
            My Orders
        </a>
    </li>

    <li>
        <a href="my_account.php">
            My Account
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
        🛍️ PetCare Shop
    </h1>

    <p>
        Find the best products for your pets.
    </p>

</div>





<div
    style="
        background:white;
        padding:20px;
        border-radius:20px;
        box-shadow:0 10px 30px rgba(0,0,0,0.07);
        margin-bottom:30px;
    "
>




    <h3
        style="
            margin-bottom:15px;
        "
    >
        🗂️ Shop by Category
    </h3>




    <div
        style="
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        "
    >


        

        <a
            href="shop.php<?php

                if (!empty($search)) {

                    echo "?search=" .
                        urlencode($search);

                }

            ?>"
            class="btn"
            style="
                padding:10px 20px;

                <?php

                if ($category_id == 0) {

                    echo "background:#333;";

                }

                ?>
            "
        >
            🛍️ All Products
        </a>



        <!-- Dynamic Categories -->

        <?php

        while (
            $category =
            $category_result->fetch_assoc()
        ) {

        ?>

            <a
                href="shop.php?category=<?php

                    echo $category["id"];

                    if (!empty($search)) {

                        echo "&search=" .
                            urlencode($search);

                    }

                ?>"
                class="btn"
                style="
                    padding:10px 20px;

                    <?php

                    if (
                        $category_id ==
                        $category["id"]
                    ) {

                        echo "background:#333;";

                    }

                    ?>
                "
            >

                <?php

                echo htmlspecialchars(
                    $category["name"]
                );

                ?>

            </a>

        <?php

        }

        ?>

    </div>




    <form
        method="GET"
        action="shop.php"
        style="
            margin-top:25px;
            display:flex;
            gap:10px;
            max-width:650px;
            margin-left:auto;
            margin-right:auto;
        "
    >


       

        <?php

        if ($category_id > 0) {

        ?>

            <input
                type="hidden"
                name="category"
                value="<?php
                echo $category_id;
                ?>"
            >

        <?php

        }

        ?>


    

        <input
            type="text"
            name="search"
            placeholder="🔍 Search product name..."
            value="<?php

                echo htmlspecialchars(
                    $search
                );

            ?>"
            style="
                flex:1;
                padding:14px 18px;
                border:2px solid #eee;
                border-radius:30px;
                font-size:15px;
                outline:none;
            "
        >




        <button
            type="submit"
            class="btn"
            style="
                padding:14px 25px;
                border:none;
                border-radius:30px;
                background:#ff6845;
                color:white;
                font-weight:bold;
                cursor:pointer;
            "
        >
            🔍 Search
        </button>


    </form>



   
    <?php

    if (!empty($search)) {

    ?>

        <div
            style="
                text-align:center;
                margin-top:15px;
            "
        >

            <span
                style="
                    color:#666;
                    font-size:14px;
                "
            >

                Search results for:

                <strong>
                    "<?php

                    echo htmlspecialchars(
                        $search
                    );

                    ?>"
                </strong>

            </span>



            <a
                href="<?php

                if ($category_id > 0) {

                    echo "shop.php?category=" .
                        $category_id;

                } else {

                    echo "shop.php";

                }

                ?>"
                style="
                    margin-left:10px;
                    color:#ff6845;
                    font-weight:bold;
                    text-decoration:none;
                "
            >
                ✖ Clear Search
            </a>

        </div>

    <?php

    }

    ?>

</div>





<div class="product-grid">


    <?php

    if ($result->num_rows > 0) {

    ?>


     

        <?php

        while (
            $product =
            $result->fetch_assoc()
        ) {

        ?>


            <div class="product-card">


               

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
                        alt="<?php

                            echo htmlspecialchars(
                                $product["name"]
                            );

                        ?>"
                        style="
                            width:100%;
                            height:220px;
                            object-fit:cover;
                            border-radius:15px;
                            margin-bottom:15px;
                        "
                    >

                <?php

                } else {

                ?>

                    <div
                        style="
                            width:100%;
                            height:220px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:#fff1e9;
                            border-radius:15px;
                            font-size:80px;
                            margin-bottom:15px;
                        "
                    >
                        🐾
                    </div>

                <?php

                }

                ?>



                

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
                            padding:6px 12px;
                            background:#fff1e9;
                            color:#d94d20;
                            border-radius:20px;
                            font-size:12px;
                            font-weight:800;
                            margin-bottom:10px;
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

                }

                ?>



                

                <h3>

                    <?php

                    echo htmlspecialchars(
                        $product["name"]
                    );

                    ?>

                </h3>



               

                <p>

                    <?php

                    $description =
                        $product[
                            "description"
                        ];


                    if (
                        strlen(
                            $description
                        ) > 80
                    ) {

                        echo htmlspecialchars(
                            substr(
                                $description,
                                0,
                                80
                            )
                        ) . "...";

                    } else {

                        echo htmlspecialchars(
                            $description
                        );

                    }

                    ?>

                </p>



               

                <h4
                    style="
                        margin:15px 0;
                        color:#ff6b35;
                        font-size:20px;
                    "
                >

                    Rs.

                    <?php

                    echo number_format(
                        $product["price"],
                        2
                    );

                    ?>

                </h4>



               

                <?php

                if (
                    $product["stock"] > 0
                ) {

                ?>


                    <p
                        style="
                            color:#218838;
                            font-weight:800;
                            margin-bottom:12px;
                        "
                    >

                        ✅

                        <?php

                        echo $product["stock"];

                        ?>

                        available

                    </p>



               

                    <a
                        href="cart.php?add=<?php

                            echo $product["id"];

                        ?>"
                        class="btn"
                    >
                        🛒 Add to Cart
                    </a>


                <?php

                } else {

                ?>


                  

                    <p
                        style="
                            color:#dc3545;
                            font-weight:800;
                            margin-bottom:12px;
                        "
                    >
                        ❌ Out of Stock
                    </p>


                    <button
                        type="button"
                        class="btn out-of-stock-btn"
                        disabled
                    >
                        ❌ Out of Stock
                    </button>


                <?php

                }


                ?>


            </div>


        <?php

        }

        ?>


    <?php

    } else {

    ?>


       
        <div
            style="
                grid-column:1 / -1;
                text-align:center;
                background:white;
                padding:60px 20px;
                border-radius:20px;
                box-shadow:0 10px 30px rgba(0,0,0,0.07);
            "
        >


            <div
                style="
                    font-size:60px;
                    margin-bottom:15px;
                "
            >
                🔍
            </div>


            <h2>
                No Products Found
            </h2>


            <p
                style="
                    margin-top:10px;
                    color:#777;
                "
            >

                <?php

                if (!empty($search)) {

                ?>

                    No products found matching

                    "<strong><?php

                        echo htmlspecialchars(
                            $search
                        );

                    ?></strong>".

                <?php

                } else {

                ?>

                    There are no products in this category yet.

                <?php

                }

                ?>

            </p>


            <br>



            <a
                href="<?php

                if ($category_id > 0) {

                    echo "shop.php?category=" .
                        $category_id;

                } else {

                    echo "shop.php";

                }

                ?>"
                class="btn"
            >
                🛍️ View Products
            </a>


        </div>


    <?php

    }

    ?>


</div>


</div>

</body>

</html>