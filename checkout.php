<?php
    include('config.php');
    if(!isset($_SESSION['AccountID'])){
        header('location:login.php');
    }
    
    // Check if cart is empty
    if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
        header('location:cart.php');
        exit;
    }
    
    // Process order submission
    if(isset($_POST['place_order'])){
        // Start transaction
        $con->begin_transaction();
        
        try {
            // Get customer ID
            $customerQuery = mysqli_query($con, "SELECT customerID FROM customer WHERE AccID = ".$_SESSION['AccountID']);
            $customer = mysqli_fetch_assoc($customerQuery);
            $customerID = $customer['customerID'];
            
            // Calculate total amount
            $total = 0;
            foreach($_SESSION['cart'] as $productID => $quantity){
                $result = mysqli_query($con, "SELECT price FROM products WHERE productID = $productID");
                $product = mysqli_fetch_assoc($result);
                $total += $product['price'] * $quantity;
            }
            
            // Insert into orders table
            $orderDate = date('Y-m-d H:i:s');
            $insertOrder = mysqli_query($con, "INSERT INTO orders (customerID, orderDate, totalAmount, status) 
                                              VALUES ($customerID, '$orderDate', $total, 'Pending')");
            
            if($insertOrder){
                $orderID = mysqli_insert_id($con);
                
                // Insert order items
                foreach($_SESSION['cart'] as $productID => $quantity){
                    $result = mysqli_query($con, "SELECT price FROM products WHERE productID = $productID");
                    $product = mysqli_fetch_assoc($result);
                    $price = $product['price'];
                    
                    $insertItem = mysqli_query($con, "INSERT INTO order_items (orderID, productID, quantity, price) 
                                                    VALUES ($orderID, $productID, $quantity, $price)");
                    
                    if(!$insertItem){
                        throw new Exception("Failed to insert order item");
                    }
                }
                
                // Commit transaction
                $con->commit();
                
                // Clear cart
                $_SESSION['cart'] = array();
                
                // Redirect to thank you page
                $_SESSION['order_success'] = true;
                header('location:order_success.php');
                exit;
            } else {
                throw new Exception("Failed to create order");
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $con->rollback();
            $error = "Error processing your order: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="images/logo-icon.jpeg">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <?php include('header&footer/header.php'); ?>
    
    <div class="container">
        <div class="form" style="width: 80%; max-width: 800px">
            <div class="form-content">
                <div class="formHeader">Checkout</div>
                
                <?php if(isset($error)): ?>
                    <div style="color: red; margin-bottom: 15px;"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div style="display: flex; justify-content: space-between;">
                    <div style="width: 48%;">
                        <h3>Shipping Information</h3>
                        
                        <?php
                            // Get customer details
                            $query = mysqli_query($con, "SELECT * FROM customer WHERE AccID = ".$_SESSION['AccountID']);
                            $customer = mysqli_fetch_assoc($query);
                        ?>
                        
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <div class="field input-field">
                                <input type="text" name="firstname" value="<?php echo $customer['firstName']; ?>" required placeholder="First Name">
                            </div>
                            
                            <div class="field input-field">
                                <input type="text" name="lastname" value="<?php echo $customer['lastName']; ?>" required placeholder="Last Name">
                            </div>
                            
                            <div class="field input-field">
                                <input type="text" name="address" value="<?php echo $customer['address']; ?>" required placeholder="Delivery Address">
                            </div>
                            
                            <div class="field input-field">
                                <input type="text" name="phone" value="<?php echo $customer['phone']; ?>" required placeholder="Phone Number">
                            </div>
                            
                            <h3>Payment Method</h3>
                            <div class="field input-field">
                                <select name="payment_method" required>
                                    <option value="">Select Payment Method</option>
                                    <option value="cash">Cash on Delivery</option>
                                    <option value="card">Credit/Debit Card</option>
                                </select>
                            </div>
                            
                            <div class="field button-field">
                                <button type="submit" name="place_order">Place Order</button>
                            </div>
                        </form>
                    </div>
                    
                    <div style="width: 48%;">
                        <h3>Order Summary</h3>
                        
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
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
                                <td><?php echo $quantity; ?></td>
                                <td>Rs. <?php echo $subtotal; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <tr>
                                <td colspan="2" align="right"><strong>Total:</strong></td>
                                <td>Rs. <?php echo $total; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include('header&footer/footer.html'); ?>
</body>
</html>
