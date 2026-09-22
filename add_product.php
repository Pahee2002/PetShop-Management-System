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



$category_result = $conn->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);


$message = "";
$message_type = "";



$name = "";
$description = "";
$price = "";
$stock = "";
$category_id = 0;
$image_url = "";



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $price = trim($_POST["price"] ?? "");
    $stock = trim($_POST["stock"] ?? "");
    $category_id = (int) ($_POST["category_id"] ?? 0);

    $new_image_url = trim($_POST["image_url"] ?? "");



    if (
        empty($name) ||
        $price === "" ||
        $stock === "" ||
        $category_id <= 0
    ) {

        $message = "Please fill all required fields.";
        $message_type = "error";

    } elseif (!is_numeric($price) || $price < 0) {

        $message = "Please enter a valid price.";
        $message_type = "error";

    } elseif (!is_numeric($stock) || $stock < 0) {

        $message = "Please enter a valid stock quantity.";
        $message_type = "error";

    } elseif (
        !empty($new_image_url) &&
        !filter_var($new_image_url, FILTER_VALIDATE_URL)
    ) {

        $message = "Please enter a valid image URL.";
        $message_type = "error";

    } else {




        $image_name = "";


    

        if (!empty($new_image_url)) {


            $image_name = $new_image_url;

        }


        elseif (
            isset($_FILES["image"]) &&
            $_FILES["image"]["error"] == 0
        ) {

            $upload_dir = "images/";


         

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }


            $original_name = $_FILES["image"]["name"];

            $tmp_name = $_FILES["image"]["tmp_name"];


         

            $extension = strtolower(
                pathinfo(
                    $original_name,
                    PATHINFO_EXTENSION
                )
            );


        

            $allowed_extensions = [
                "jpg",
                "jpeg",
                "png",
                "gif",
                "webp"
            ];


         

            if (!in_array($extension, $allowed_extensions)) {

                $message =
                    "Only JPG, JPEG, PNG, GIF and WEBP images are allowed.";

                $message_type = "error";

            } else {


           

                $safe_name = preg_replace(
                    "/[^a-zA-Z0-9._-]/",
                    "_",
                    $original_name
                );


                $image_name =
                    time() .
                    "_" .
                    $safe_name;


                $image_path =
                    $upload_dir .
                    $image_name;


               

                if (
                    !move_uploaded_file(
                        $tmp_name,
                        $image_path
                    )
                ) {

                    $message = "Image upload failed.";
                    $message_type = "error";

                    $image_name = "";

                }

            }

        }


     

        if ($message_type != "error") {

            $stmt = $conn->prepare(
                "INSERT INTO products
                (
                    category_id,
                    name,
                    description,
                    price,
                    stock,
                    image
                )
                VALUES (?, ?, ?, ?, ?, ?)"
            );


            $stmt->bind_param(
                "issdis",
                $category_id,
                $name,
                $description,
                $price,
                $stock,
                $image_name
            );


          

            if ($stmt->execute()) {

                $message =
                    "Product added successfully!";

                $message_type = "success";


            
              

                $name = "";
                $description = "";
                $price = "";
                $stock = "";
                $category_id = 0;
                $image_url = "";

            } else {

                $message =
                    "Failed to add product.";

                $message_type = "error";

            }


            $stmt->close();

        }

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

    <title>Add Product - PetCare</title>


   

    <link
        rel="stylesheet"
        href="css/style.css"
    >


   

    <link
        rel="stylesheet"
        href="css/add_product.css"
    >

     <link
        rel="stylesheet"
        href="css/edit_product.css"
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





<div class="auth-page">


    <div class="auth-card">


        <h2>
            ➕ Add Product
        </h2>


        <p>
            Add a new pet product to your shop.
        </p>



       

        <?php if (!empty($message)) { ?>

            <div
                class="<?php
                echo (
                    $message_type == "success"
                )
                    ? "success-message"
                    : "error-message";
                ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php } ?>



    

        <form
            method="POST"
            enctype="multipart/form-data"
        >


          

            <div class="form-group">

                <label>
                    Product Name *
                </label>

                <input
                    type="text"
                    name="name"
                    value="<?php
                    echo htmlspecialchars($name);
                    ?>"
                    placeholder="Enter product name"
                    required
                >

            </div>



           

            <div class="form-group">

                <label>
                    Category *
                </label>

                <select
                    name="category_id"
                    required
                >

                    <option value="">
                        -- Select Category --
                    </option>


                    <?php while (
                        $category =
                        $category_result->fetch_assoc()
                    ) { ?>

                        <option
                            value="<?php
                            echo $category["id"];
                            ?>"
                            <?php
                            echo (
                                $category_id
                                == $category["id"]
                            )
                                ? "selected"
                                : "";
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $category["name"]
                            );
                            ?>

                        </option>

                    <?php } ?>


                </select>

            </div>



            

            <div class="form-group">

                <label>
                    Description
                </label>

                <textarea
                    name="description"
                    class="description-input"
                    placeholder="Enter product description..."
                ><?php
                echo htmlspecialchars($description);
                ?></textarea>

            </div>



          

            <div class="form-group">

                <label>
                    Price (Rs.) *
                </label>

                <input
                    type="number"
                    name="price"
                    step="0.01"
                    min="0"
                    value="<?php
                    echo htmlspecialchars($price);
                    ?>"
                    placeholder="Enter price"
                    required
                >

            </div>




            <div class="form-group">

                <label>
                    Stock Quantity *
                </label>

                <input
                    type="number"
                    name="stock"
                    min="0"
                    value="<?php
                    echo htmlspecialchars($stock);
                    ?>"
                    placeholder="Enter stock quantity"
                    required
                >

            </div>



         

            <div class="form-group">

                <label>
                    Product Image
                </label>


                <div class="image-upload-box">


                    <!-- UPLOAD IMAGE -->

                    <div class="upload-section">

                        <label class="sub-label">
                            📁 Upload Image
                        </label>

                        <input
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png,.gif,.webp"
                        >

                    </div>



                    <!-- OR -->

                    <div class="or-text">
                        OR
                    </div>



                    <!-- IMAGE URL -->

                    <div class="url-section">

                        <label class="sub-label">
                            🔗 Image URL
                        </label>

                        <input
                            type="url"
                            name="image_url"
                            class="image-url-input"
                            placeholder="https://example.com/product-image.jpg"
                            value="<?php
                            echo htmlspecialchars($image_url);
                            ?>"
                        >

                    </div>



                    <p class="image-help">

                        Upload an image OR enter an image URL.

                    </p>


                </div>

            </div>




            <button
                type="submit"
                class="btn"
            >

                ➕ Add Product

            </button>


            <a
                href="products.php"
                class="btn"
                style="
                    background:#555;
                    margin-left:10px;
                "
            >

                ← Back

            </a>


        </form>


    </div>


</div>


</body>

</html>