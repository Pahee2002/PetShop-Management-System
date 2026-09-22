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
 
 

 
if (!isset($_SESSION["cart"])) { 
    $_SESSION["cart"] = []; 
} 
 
 
 
if (isset($_GET["add"])) { 
 
    $product_id = (int) $_GET["add"]; 
 
    $stmt = $conn->prepare( 
        "SELECT id, name, price, stock, image 
         FROM products 
         WHERE id = ? AND stock > 0" 
    ); 
 
    $stmt->bind_param("i", $product_id); 
    $stmt->execute(); 
 
    $result = $stmt->get_result(); 
 
    if ($result->num_rows == 1) { 
 
        $product = $result->fetch_assoc(); 
 
        if (isset($_SESSION["cart"][$product_id])) { 
 
            if ( 
                $_SESSION["cart"][$product_id]["quantity"] 
                < $product["stock"] 
            ) { 
 
                $_SESSION["cart"][$product_id]["quantity"]++; 
 
            } 
 
        } else { 
 
            $_SESSION["cart"][$product_id] = [ 
                "id" => $product["id"], 
                "name" => $product["name"], 
                "price" => $product["price"], 
                "stock" => $product["stock"], 
                "image" => $product["image"], 
                "quantity" => 1 
            ]; 
 
        } 
 
    } 
 
    $stmt->close(); 
 
    header("Location: cart.php"); 
    exit; 
} 
 
 

 
if (isset($_GET["increase"])) { 
 
    $product_id = (int) $_GET["increase"]; 
 
    if (isset($_SESSION["cart"][$product_id])) { 
 
        if ( 
            $_SESSION["cart"][$product_id]["quantity"] 
            < $_SESSION["cart"][$product_id]["stock"] 
        ) { 
 
            $_SESSION["cart"][$product_id]["quantity"]++; 
 
        } 
 
    } 
 
    header("Location: cart.php"); 
    exit; 
} 
 
 

 
if (isset($_GET["decrease"])) { 
 
    $product_id = (int) $_GET["decrease"]; 
 
    if (isset($_SESSION["cart"][$product_id])) { 
 
        $_SESSION["cart"][$product_id]["quantity"]--; 
 
        if ( 
            $_SESSION["cart"][$product_id]["quantity"] <= 0 
        ) { 
 
            unset($_SESSION["cart"][$product_id]); 
 
        } 
 
    } 
 
    header("Location: cart.php"); 
    exit; 
} 
 

 
if (isset($_GET["remove"])) { 
 
    $product_id = (int) $_GET["remove"]; 
 
    if (isset($_SESSION["cart"][$product_id])) { 
 
        unset($_SESSION["cart"][$product_id]); 
 
    } 
 
    header("Location: cart.php"); 
    exit; 
} 
 
 
/* Calculate total */ 
 
$total = 0; 
 
foreach ($_SESSION["cart"] as $item) { 
 
    $total += 
        $item["price"] * $item["quantity"]; 
 
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
 
    <title>My Cart - PetCare</title> 
 
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
            <a 
                href="logout.php" 
                class="login-btn" 
            > 
                LOGOUT 
            </a> 
        </li> 
 
    </ul> 
 
</nav> 
 
  
<div class="dashboard"> 
 
    <div class="dashboard-header"> 
 
        <h1> 
            🛒 My Shopping Cart 
        </h1> 
 
        <p> 
            Review your selected pet products. 
        </p> 
 
    </div> 
 
 
    <?php if (!empty($_SESSION["cart"])): ?> 
 
        <div class="product-table-wrapper"> 
 
            <table class="product-table"> 
 
                <thead> 
 
                    <tr> 
 
                        <th>Product</th> 
                        <th>Price</th> 
                        <th>Quantity</th> 
                        <th>Subtotal</th> 
                        <th>Action</th> 
 
                    </tr> 
 
                </thead> 
 
 
                <tbody> 
 
                    <?php foreach ( 
                        $_SESSION["cart"] as $item 
                    ): ?> 
 
                        <tr> 
 
                            <td> 
 
                                <?php if ( 
                                    !empty($item["image"]) 
                                ): ?> 
 
                                    <?php 
 
                                    $product_image = 
                                        $item["image"]; 
 
 
                                   
 
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
                                            $item["name"] 
                                        ); 
                                        ?>" 
                                        style=" 
                                            width:70px; 
                                            height:70px; 
                                            object-fit:cover; 
                                            border-radius:10px; 
                                            vertical-align:middle; 
                                            margin-right:10px; 
                                        " 
                                    > 
 
                                <?php endif; ?> 
 
                                <strong> 
 
                                    <?php 
                                    echo htmlspecialchars( 
                                        $item["name"] 
                                    ); 
                                    ?> 
 
                                </strong> 
 
                            </td> 
 
 
                            <td> 
 
                                Rs. 
                                <?php 
                                echo number_format( 
                                    $item["price"], 
                                    2 
                                ); 
                                ?> 
 
                            </td> 
 
 
                            <td> 
 
                                <a 
                                    href="cart.php?decrease=<?php 
                                    echo $item["id"]; 
                                    ?>" 
                                    class="edit-btn" 
                                > 
                                    − 
                                </a> 
 
 
                                <strong> 
 
                                    <?php 
                                    echo $item["quantity"]; 
                                    ?> 
 
                                </strong> 
 
 
                                <a 
                                    href="cart.php?increase=<?php 
                                    echo $item["id"]; 
                                    ?>" 
                                    class="edit-btn" 
                                > 
                                    + 
                                </a> 
 
                            </td> 
 
 
                            <td> 
 
                                <strong> 
 
                                    Rs. 
                                    <?php 
                                    echo number_format( 
                                        $item["price"] 
                                        * $item["quantity"], 
                                        2 
                                    ); 
                                    ?> 
 
                                </strong> 
 
                            </td> 
 
 
                            <td> 
 
                                <a 
                                    href="cart.php?remove=<?php 
                                    echo $item["id"]; 
                                    ?>" 
                                    class="delete-btn" 
                                > 
                                    🗑️ Remove 
                                </a> 
 
                            </td> 
 
                        </tr> 
 
                    <?php endforeach; ?> 
 
                </tbody> 
 
            </table> 
 
        </div> 
 
 
       
 
        <div 
            class="dashboard-header" 
            style=" 
                margin-top:25px; 
                text-align:right; 
            " 
        > 
 
            <h2> 
 
                Total: 
                <span class="price"> 
 
                    Rs. 
                    <?php 
                    echo number_format( 
                        $total, 
                        2 
                    ); 
                    ?> 
 
                </span> 
 
            </h2> 
 
 
            <br> 
 
 
            <a 
                href="shop.php" 
                class="btn" 
            > 
                ← Continue Shopping 
            </a> 
 
 
            <a 
                href="checkout.php" 
                class="btn" 
                style="margin-left:10px;" 
            > 
                Proceed to Checkout → 
            </a> 
 
        </div> 
 
 
    <?php else: ?> 
 
 
        <div 
            class="dashboard-header" 
            style="text-align:center;" 
        > 
 
            <div 
                class="auth-logo" 
            > 
                🛒 
            </div> 
 
 
            <h2> 
                Your Cart is Empty 
            </h2> 
 
 
            <p> 
                You haven't added any products yet. 
            </p> 
 
 
            <a 
                href="shop.php" 
                class="btn" 
            > 
                🛍️ Start Shopping 
            </a> 
 
        </div> 
 
 
    <?php endif; ?> 
 
 
</div> 
 
 
</body> 
 
</html>