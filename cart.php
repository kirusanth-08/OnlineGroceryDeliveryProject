<?php
    include('config.php');
    if(!isset($_SESSION['AccountID'])){
        header('location:login.php');
    }
    
    // Initialize cart if it doesn't exist
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
    }
    
    // Add product to cart
    if(isset($_GET['add_to_cart'])){
        $productID = $_GET['add_to_cart'];
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
        
        // If product already in cart, update quantity
        if(isset($_SESSION['cart'][$productID])){
            $_SESSION['cart'][$productID] += $quantity;
        } else {
            $_SESSION['cart'][$productID] = $quantity;
        }
        
        header('location:cart.php');
    }
    
    // Remove product from cart
    if(isset($_GET['remove'])){
        $productID = $_GET['remove'];
        unset($_SESSION['cart'][$productID]);
        header('location:cart.php');
    }
    
    // Clear cart
    if(isset($_GET['clear'])){
        $_SESSION['cart'] = array();
        header('location:cart.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo-icon.jpeg">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <?php include('header&footer/header.php'); ?>
    
    <div class="container">
        <div class="form" style="width: 80%; max-width: 800px">
            <div class="form-content">
                <div class="formHeader">Your Shopping Cart</div>
                
                <?php if(empty($_SESSION['cart'])): ?>
                    <p>Your cart is empty. <a href="index.php">Continue shopping</a></p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        
                        <?php 
                            $total = 0;
                            foreach($_SESSION['cart'] as $productID => $quantity):
                                $result = mysqli_query($con, "SELECT * FROM products WHERE productID = $productID");
                                $product = mysqli_fetch_assoc($result);
                                $subtotal = $product['price'] * $quantity;
                                $total += $subtotal;
                        ?>
                        <tr>
                            <td><?php echo $product['productName']; ?></td>
                            <td>Rs. <?php echo $product['price']; ?></td>
                            <td><?php echo $quantity; ?></td>
                            <td>Rs. <?php echo $subtotal; ?></td>
                            <td><a href="cart.php?remove=<?php echo $productID; ?>">Remove</a></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr>
                            <td colspan="3" align="right"><strong>Total:</strong></td>
                            <td>Rs. <?php echo $total; ?></td>
                            <td></td>
                        </tr>
                    </table>
                    
                    <div style="margin-top: 20px;">
                        <a href="cart.php?clear=true" class="field button-field" style="display: inline-block; margin-right: 10px;">Clear Cart</a>
                        <a href="checkout.php" class="field button-field" style="display: inline-block;">Checkout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include('header&footer/footer.html'); ?>
</body>
</html>
