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


$user_id = $_SESSION["user_id"];




$stmt = $conn->prepare(
    "UPDATE notifications
     SET is_read = 1
     WHERE user_id = ?
     AND is_read = 0"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$stmt->close();




$stmt = $conn->prepare(
    "SELECT
        id,
        order_id,
        message,
        is_read,
        created_at
     FROM notifications
     WHERE user_id = ?
     ORDER BY id DESC"
);

$stmt->bind_param(
    "i",
    $user_id
);

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

    <title>Messages - PetCare</title>

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

            <a href="customer_dashboard.php">
                Dashboard
            </a>

        </li>


     

        <li>

            <a href="shop.php">
                🛍️ Shop
            </a>

        </li>


  
        <li>

            <a href="cart.php">
                🛒 Cart
            </a>

        </li>


     

        <li>

            <a href="orders.php">
                📦 My Orders
            </a>

        </li>


     

        <li>

            <a href="notifications.php">
                🔔 Messages
            </a>

        </li>


    

        <li>

            <a href="my_account.php">
                👤 My Account
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


    <!-- PAGE HEADER -->

    <div class="dashboard-header">

        <h1>
            🔔 Messages
        </h1>

        <p>
            Here you can view your order updates
            and notifications.
        </p>

    </div>



    <?php if ($result->num_rows > 0) { ?>


        <?php while (
            $notification =
            $result->fetch_assoc()
        ) { ?>



            <div
                class="menu-item"
                style="
                    margin-bottom:20px;
                    background:white;
                    border-left:5px solid #ff6b35;
                "
            >


             

                <h3>

                    <?php

                    echo htmlspecialchars(
                        $notification["message"]
                    );

                    ?>

                </h3>



          

                <?php if (
                    !empty(
                        $notification["order_id"]
                    )
                ) { ?>

                    <p
                        style="
                            margin-top:10px;
                        "
                    >

                        📦 Order #

                        <?php

                        echo $notification[
                            "order_id"
                        ];

                        ?>

                    </p>

                <?php } ?>



        

                <p
                    style="
                        color:#888;
                        font-size:13px;
                        margin-top:8px;
                    "
                >

                    🕒

                    <?php

                    echo date(
                        "d M Y, h:i A",
                        strtotime(
                            $notification[
                                "created_at"
                            ]
                        )
                    );

                    ?>

                </p>


            </div>


        <?php } ?>


    <?php } else { ?>


       

        <div
            class="dashboard-header"
            style="
                text-align:center;
            "
        >

            <div
                style="
                    font-size:60px;
                    margin-bottom:15px;
                "
            >
                🔔
            </div>


            <h2>
                No Messages
            </h2>


            <p
                style="
                    margin-top:10px;
                    color:#777;
                "
            >
                You don't have any notifications yet.
            </p>


            <a
                href="customer_dashboard.php"
                class="btn"
                style="
                    margin-top:20px;
                "
            >
                🏠 Back to Dashboard
            </a>

        </div>


    <?php } ?>


</div>



</body>

</html>

<?php

$stmt->close();

?>