<?php
include('db.php');

if(isset($_POST['name'], $_POST['category'], $_POST['size'], $_POST['quantity'], $_POST['price'])){

    $name = $_POST['name'];
    $category = $_POST['category'];
    $size = $_POST['size'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $query = "INSERT INTO products (name, category, size, quantity, price)
              VALUES ('$name', '$category', '$size', '$quantity', '$price')";

    if(mysqli_query($conn, $query)){
        header("Location: admin.php?msg=Product added successfully");
        exit();
    }else{
        echo "Error: " . mysqli_error($conn);
    }

}else{
    echo "Invalid form submission.";
}
?>