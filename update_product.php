<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    $query = "UPDATE products SET 
              name='$name', 
              category='$category', 
              size='$size', 
              quantity='$quantity', 
              price='$price' 
              WHERE id='$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: admin.php?msg=updated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>