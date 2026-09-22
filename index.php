<?php

session_start();

require_once "config/db.php";



$is_logged_in = isset($_SESSION["user_id"]);

$user_role = "";

if ($is_logged_in) {
    $user_role = $_SESSION["user_role"];
}




$category_result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

$category_ids = [];

while ($category = $category_result->fetch_assoc()) {

    $category_ids[
        strtolower(trim($category["name"]))
    ] = $category["id"];

}




$product_result = $conn->query(
    "SELECT
        products.id,
        products.name,
        products.description,
        products.price,
        products.stock,
        products.image,
        products.category_id,
        categories.name AS category_name
     FROM products
     LEFT JOIN categories
        ON products.category_id = categories.id
     ORDER BY products.id DESC
     LIMIT 3"
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

    <title>PetCare - Pet Shop</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>




<nav class="navbar">

    <a
        href="index.php"
        class="logo"
    >
        🐾 Pet<span>Care</span>
    </a>


    <ul class="nav-links">


    

        <li>
            <a href="index.php">
                Home
            </a>
        </li>


    

        <li>
            <a href="#categories">
                Categories
            </a>
        </li>


  

        <li>
            <a href="#products">
                Products
            </a>
        </li>


        <?php if (!$is_logged_in) { ?>


      

            <li>

                <a
                    href="login.php"
                    class="login-btn"
                >
                    LOGIN
                </a>

            </li>


        <?php } else { ?>


            <?php if ($user_role == "customer") { ?>


              

                <li>

                    <a href="customer_dashboard.php">
                        🏠 Customer Dashboard
                    </a>

                </li>


              

                <li>

                    <a href="shop.php">
                        🛍️ Shop
                    </a>

                </li>


           

                <li>

                    <a href="orders.php">
                        📦 My Orders
                    </a>

                </li>


              

                <li>

                    <a href="my_account.php">
                        👤 My Account
                    </a>

                </li>


            <?php } else { ?>


              

                <li>

                    <a href="admin_dashboard.php">
                        ⚙️ Admin Dashboard
                    </a>

                </li>


            <?php } ?>



            <li>

                <a
                    href="logout.php"
                    class="login-btn"
                >
                    LOGOUT
                </a>

            </li>


        <?php } ?>


    </ul>

</nav>





<section
    class="hero"
    id="home"
>


    <div class="hero-content">


        <p>
            🐾 Welcome to PetCare
        </p>


        <h1>

            Everything Your

            <span>
                Pet Loves.
            </span>

        </h1>


        <p>

            Discover quality food, toys, accessories
            and everything your furry friends need
            to stay happy and healthy.

        </p>



      

        <?php if (!$is_logged_in) { ?>


            <a
                href="login.php"
                class="btn"
            >
                🛍️ Shop Now
            </a>


        <?php } elseif ($user_role == "customer") { ?>


            <a
                href="shop.php"
                class="btn"
            >
                🛍️ Shop Now
            </a>


        <?php } else { ?>


            <a
                href="admin_dashboard.php"
                class="btn"
            >
                ⚙️ Dashboard
            </a>


        <?php } ?>


    </div>



    <div>

        <img
            src="https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=700&q=80"
            alt="Happy Dog"
            class="hero-image"
        >

    </div>


</section>





<section
    class="products"
    id="categories"
>


    <div class="section-title">


        <h2>

            🐾 Shop by Category

        </h2>


        <p>

            Everything your pets need in one place.

        </p>


    </div>



    <div class="product-grid">


    
        <?php

        if (!$is_logged_in) {

            $dog_link = "login.php";

        } elseif ($user_role == "customer") {

            if (isset($category_ids["dog"])) {

                $dog_link =
                    "shop.php?category=" .
                    $category_ids["dog"];

            } else {

                $dog_link = "shop.php";

            }

        } else {

            $dog_link = "admin_dashboard.php";

        }

        ?>


        <a
            href="<?php echo $dog_link; ?>"
            style="
                text-decoration:none;
                color:inherit;
            "
        >


            <div class="product-card">


                <div class="category-icon">

                    🐶

                </div>



                <div class="product-info">


                    <h3>

                        Dog Products

                    </h3>


                    <p>

                        Food, toys and accessories
                        for your dogs.

                    </p>


                </div>


            </div>


        </a>



        <?php

        if (!$is_logged_in) {

            $cat_link = "login.php";

        } elseif ($user_role == "customer") {

            if (isset($category_ids["cat"])) {

                $cat_link =
                    "shop.php?category=" .
                    $category_ids["cat"];

            } else {

                $cat_link = "shop.php";

            }

        } else {

            $cat_link = "admin_dashboard.php";

        }

        ?>


        <a
            href="<?php echo $cat_link; ?>"
            style="
                text-decoration:none;
                color:inherit;
            "
        >


            <div class="product-card">


                <div class="category-icon">

                    🐱

                </div>



                <div class="product-info">


                    <h3>

                        Cat Products

                    </h3>


                    <p>

                        Quality products for
                        happy cats.

                    </p>


                </div>


            </div>


        </a>



    

        <?php

        if (!$is_logged_in) {

            $bird_link = "login.php";

        } elseif ($user_role == "customer") {

            if (isset($category_ids["bird"])) {

                $bird_link =
                    "shop.php?category=" .
                    $category_ids["bird"];

            } else {

                $bird_link = "shop.php";

            }

        } else {

            $bird_link = "admin_dashboard.php";

        }

        ?>


        <a
            href="<?php echo $bird_link; ?>"
            style="
                text-decoration:none;
                color:inherit;
            "
        >


            <div class="product-card">


                <div class="category-icon">

                    🐦

                </div>



                <div class="product-info">


                    <h3>

                        Bird Products

                    </h3>


                    <p>

                        Food and accessories
                        for your birds.

                    </p>


                </div>


            </div>


        </a>



   

        <?php

        if (!$is_logged_in) {

            $fish_link = "login.php";

        } elseif ($user_role == "customer") {

            if (isset($category_ids["fish"])) {

                $fish_link =
                    "shop.php?category=" .
                    $category_ids["fish"];

            } else {

                $fish_link = "shop.php";

            }

        } else {

            $fish_link = "admin_dashboard.php";

        }

        ?>


        <a
            href="<?php echo $fish_link; ?>"
            style="
                text-decoration:none;
                color:inherit;
            "
        >


            <div class="product-card">


                <div class="category-icon">

                    🐠

                </div>



                <div class="product-info">


                    <h3>

                        Fish Products

                    </h3>


                    <p>

                        Aquarium supplies and
                        fish care products.

                    </p>


                </div>


            </div>


        </a>


    </div>


</section>





<section
    class="products"
    id="products"
>


    <div class="section-title">


        <h2>

            ⭐ Featured Products

        </h2>


        <p>

            Popular products for your lovely pets.

        </p>


    </div>



    <div class="product-grid">


        <?php if (
            $product_result &&
            $product_result->num_rows > 0
        ) { ?>


            <?php while (
                $product =
                $product_result->fetch_assoc()
            ) { ?>


                <div class="product-card">


                 


                    <?php if (
                        !empty(
                            $product["image"]
                        )
                    ) { ?>


                        <?php

                        

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
                        >


                    <?php } else { ?>


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


                    <?php } ?>



                    <div class="product-info">


                      


                        <?php if (
                            !empty(
                                $product[
                                    "category_name"
                                ]
                            )
                        ) { ?>


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


                        <?php } ?>



                       


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
                                ) > 70
                            ) {

                                echo htmlspecialchars(
                                    substr(
                                        $description,
                                        0,
                                        70
                                    )
                                ) . "...";

                            } else {

                                echo htmlspecialchars(
                                    $description
                                );

                            }

                            ?>


                        </p>



                    


                        <div class="price">


                            Rs.


                            <?php

                            echo number_format(
                                $product["price"],
                                2
                            );

                            ?>


                        </div>



                       


                        <?php if (
                            !$is_logged_in
                        ) { ?>


                            <a
                                href="login.php"
                                class="btn"
                            >

                                🔐 Login to Shop

                            </a>


                        <?php } elseif (
                            $user_role ==
                            "customer"
                        ) { ?>


                            <?php if (
                                $product["stock"] > 0
                            ) { ?>


                                <a
                                    href="cart.php?add=<?php
                                    echo $product["id"];
                                    ?>"
                                    class="btn"
                                >

                                    🛒 Add to Cart

                                </a>


                            <?php } else { ?>


                                <button
                                    type="button"
                                    class="btn out-of-stock-btn"
                                    disabled
                                >

                                    ❌ Out of Stock

                                </button>


                            <?php } ?>


                        <?php } else { ?>


                            <a
                                href="admin_dashboard.php"
                                class="btn"
                            >

                                ⚙️ Admin Dashboard

                            </a>


                        <?php } ?>


                    </div>


                </div>


            <?php } ?>


        <?php } else { ?>




            <div
                style="
                    grid-column:1 / -1;
                    text-align:center;
                    background:white;
                    padding:50px 20px;
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

                    🐾

                </div>


                <h2>

                    No Products Available

                </h2>


                <p
                    style="
                        margin-top:10px;
                        color:#777;
                    "
                >

                    Products will appear here
                    when they are added.

                </p>


            </div>


        <?php } ?>


    </div>


</section>





<section class="products">


    <div class="section-title">


        <h2>

            ❤️ Why Choose PetCare?

        </h2>


    </div>



    <div class="product-grid">





        <div class="menu-item">


            <div class="icon">

                🚚

            </div>


            <h3>

                Fast Delivery

            </h3>


            <p>

                Get your pet products
                delivered quickly.

            </p>


        </div>



    

        <div class="menu-item">


            <div class="icon">

                💖

            </div>


            <h3>

                Pet Friendly

            </h3>


            <p>

                Products selected
                with pets in mind.

            </p>


        </div>






        <div class="menu-item">


            <div class="icon">

                🔒

            </div>


            <h3>

                Secure Shopping

            </h3>


            <p>

                Safe and simple
                shopping experience.

            </p>


        </div>



  


        <div class="menu-item">


            <div class="icon">

                📞

            </div>


            <h3>

                Customer Support

            </h3>


            <p>

                We are here to help
                when you need us.

            </p>


        </div>


    </div>


</section>





<footer>


    <h2>🐾 PetCare</h2>


    <p>

        Making pets happier,
        one product at a time. ❤️

    </p>


    <h3>

        📞 Contact Us

    </h3>


    <p>

        📍 Jaffna, Sri Lanka

    </p>


    <p>

        📞 +94 77 123 4567

    </p>


    <p>

        ✉️ info@petcare.com

    </p>


    <h3>

        🕒 Opening Hours

    </h3>


    <p>

        Monday - Saturday: 9:00 AM - 6:00 PM

    </p>


    <p>

        Sunday: Closed

    </p>


    <p>

        © 2026 PetCare Pet Shop

    </p>


</footer>


</body>

</html>