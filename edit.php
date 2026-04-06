<?php
include('db.php');

// Fetch the existing data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    $product = mysqli_fetch_assoc($result);
}

// Update the data if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    $sql = "UPDATE products SET name='$name', category='$category', size='$size', quantity='$quantity', price='$price' WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php?msg=updated");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; display: flex; justify-content: center; padding-top: 50px; }
        .edit-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 400px; }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .save-btn { background: #3498db; color: white; border: none; padding: 12px; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .cancel-link { display: block; text-align: center; margin-top: 15px; color: #7f8c8d; text-decoration: none; }
    </style>
</head>
<body>
    <div class="edit-container">
        <h2>Edit Product #<?php echo $product['id']; ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
            <label>Category</label>
            <input type="text" name="category" value="<?php echo $product['category']; ?>" required>
            <label>Size</label>
            <input type="text" name="size" value="<?php echo $product['size']; ?>" required>
            <label>Quantity</label>
            <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" required>
            <label>Price (Ksh)</label>
            <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
            
            <button type="submit" class="save-btn">Update Product</button>
            <a href="admin.php" class="cancel-link">Cancel and Go Back</a>
        </form>
    </div>
</body>
</html>