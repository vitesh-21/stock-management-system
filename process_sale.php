<?php
include('db.php');

$product_id = $_POST['product_id'];
$quantity_sold = $_POST['quantity_sold'];
$payment_method = $_POST['payment_method'];

// Get product info
$product = mysqli_query($conn, "SELECT * FROM products WHERE id='$product_id'");
$row = mysqli_fetch_assoc($product);

if(!$row){
    echo "Product not found";
    exit();
}

$price = $row['price'];
$current_stock = $row['quantity'];

if($quantity_sold > $current_stock){
    echo "Not enough stock";
    exit();
}

$total_amount = $price * $quantity_sold;

// Save sale
mysqli_query($conn,"INSERT INTO sales(product_id, quantity_sold, payment_method, total_amount)
VALUES('$product_id','$quantity_sold','$payment_method','$total_amount')");

// Update stock
$new_stock = $current_stock - $quantity_sold;

mysqli_query($conn,"UPDATE products SET quantity='$new_stock' WHERE id='$product_id'");

header("Location: admin.php?msg=Sale recorded successfully");
?>