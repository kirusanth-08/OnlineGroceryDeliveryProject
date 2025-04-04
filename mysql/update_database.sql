-- Script to update database schema for password hashing

-- First, alter the account table to increase the password field size for storing bcrypt hashes
ALTER TABLE account MODIFY AccPassword VARCHAR(255) NOT NULL;

-- Create orders table to store order information
CREATE TABLE IF NOT EXISTS orders (
    orderID INT NOT NULL AUTO_INCREMENT,
    customerID INT NOT NULL,
    orderDate DATETIME NOT NULL,
    totalAmount DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    PRIMARY KEY (orderID),
    FOREIGN KEY (customerID) REFERENCES customer(customerID)
);

-- Create order_items table to store ordered products
CREATE TABLE IF NOT EXISTS order_items (
    itemID INT NOT NULL AUTO_INCREMENT,
    orderID INT NOT NULL,
    productID INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (itemID),
    FOREIGN KEY (orderID) REFERENCES orders(orderID),
    FOREIGN KEY (productID) REFERENCES products(productID)
);

-- Update admin password to use bcrypt
-- Note: This is a placeholder. In real implementation, you would generate a proper bcrypt hash
-- UPDATE account SET AccPassword = '$2y$10$yoursalthereadminhashedpassword' WHERE AccID = 1;
