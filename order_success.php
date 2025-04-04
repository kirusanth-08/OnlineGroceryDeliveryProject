<?php
    include('config.php');
    
    // Redirect if not coming from checkout
    if(!isset($_SESSION['order_success'])){
        header('location:index.php');
        exit;
    }
    
    // Clear the flag
    unset($_SESSION['order_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo-icon.jpeg">
    <title>Order Success</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <?php include('header&footer/header.php'); ?>
    
    <div class="container">
        <div class="thank">
            <h1>Thank You for Your Order!</h1>
            <p style="text-align: center; margin: 20px 0;">Your order has been placed successfully. We'll process it soon.</p>
            <div style="text-align: center; margin-top: 30px;">
                <a href="index.php">Continue Shopping</a>
            </div>
        </div>
    </div>
    
    <?php include('header&footer/footer.html'); ?>
</body>
</html>
