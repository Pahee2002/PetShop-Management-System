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
 

 
$sql = "SELECT id, name, price, stock, image 
        FROM products 
        ORDER BY stock ASC, id DESC"; 
 
$result = $conn->query($sql); 
 
?> 
 
<!DOCTYPE html> 
<html lang="en"> 
 
<head> 
 
    <meta charset="UTF-8"> 
 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
 
    <title>Stock Management - PetCare</title> 
 
    <link rel="stylesheet" href="css/style.css"> 
 
</head> 
 
<body> 
 
<nav class="navbar"> 
 
    <a href="admin_dashboard.php" class="logo"> 
        Pet<span>Care</span> 
    </a> 
 
    <ul class="nav-links"> 
 
        <li> 
            <a href="admin_dashboard.php">Dashboard</a> 
        </li> 
 
        <li> 
            <a href="products.php">Products</a> 
        </li> 
 
        <li> 
            <a href="categories.php">Categories</a> 
        </li> 
 
        <li> 
            <a href="customers.php">Customers</a> 
        </li> 
 
        <li> 
            <a href="orders.php">Orders</a> 
        </li> 
 
        <li> 
            <a href="logout.php" class="login-btn">Logout</a> 
        </li> 
 
    </ul> 
 
</nav> 
 
 
<div class="dashboard"> 
 
    <div class="dashboard-header"> 
 
        <h1>Stock Management</h1> 
 
        <p> 
            Monitor product stock levels and availability. 
        </p> 
 
    </div> 
 
 
    <div class="order-table-wrapper"> 
 
        <?php if ($result && $result->num_rows > 0) { ?> 
 
            <table class="order-table"> 
 
                <thead> 
 
                    <tr> 
 
                        <th>Product</th> 
 
                        <th>Price</th> 
 
                        <th>Current Stock</th> 
 
                        <th>Stock Status</th> 
 
                    </tr> 
 
                </thead> 
 
 
                <tbody> 
 
                    <?php while ($product = $result->fetch_assoc()) { ?> 
 
                        <tr> 
 
                            <td> 
 
                                <?php if (!empty($product["image"])) { ?> 
 
                                    <?php 
 
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
                                        src="<?php echo htmlspecialchars($product_image_src); ?>" 
                                        alt="<?php echo htmlspecialchars($product["name"]); ?>" 
                                        style=" 
                                            width:60px; 
                                            height:60px; 
                                            object-fit:cover; 
                                            border-radius:10px; 
                                            vertical-align:middle; 
                                            margin-right:10px; 
                                        " 
                                    > 
 
                                <?php } else { ?> 
 
                                    <span style="font-size:35px; margin-right:10px;"> 
                                        🐾 
                                    </span> 
 
                                <?php } ?> 
 
                                <strong> 
                                    <?php echo htmlspecialchars($product["name"]); ?> 
                                </strong> 
 
                            </td> 
 
 
                            <td> 
 
                                Rs. 
                                <?php echo number_format($product["price"], 2); ?> 
 
                            </td> 
 
 
                            <td> 
 
                                <strong> 
                                    <?php echo $product["stock"]; ?> 
                                </strong> 
 
                            </td> 
 
 
                            <td> 
 
                                <?php 
 
                                if ($product["stock"] == 0) { 
 
                                    echo '<span class="status-badge status-cancelled"> 
                                            ❌ Out of Stock 
                                          </span>'; 
 
                                } elseif ($product["stock"] <= 5) { 
 
                                    echo '<span class="status-badge status-pending"> 
                                            ⚠️ Low Stock 
                                          </span>'; 
 
                                } else { 
 
                                    echo '<span class="status-badge status-confirmed"> 
                                            ✅ In Stock 
                                          </span>'; 
                                } 
 
                                ?> 
 
                            </td> 
 
                        </tr> 
 
                    <?php } ?> 
 
                </tbody> 
 
            </table> 
 
        <?php } else { ?> 
 
            <div class="no-orders"> 
 
                <h2>No Products Found</h2> 
 
                <p> 
                    There are no products available to manage. 
                </p> 
 
                <br> 
 
                <a href="add_product.php" class="btn"> 
                    ➕ Add Product 
                </a> 
 
            </div> 
 
        <?php } ?> 
 
    </div> 
 
</div> 
 
</body> 
 
</html>