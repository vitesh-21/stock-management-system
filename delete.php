<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Delete the product
    $sql = "DELETE FROM products WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php?msg=deleted");
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>