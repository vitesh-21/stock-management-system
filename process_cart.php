<?php
session_start();
include('db.php');

// Get the JSON data sent from the POS screen
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $cart = $data['cart'];
    $payment_method = $data['payment_method'];
    $total_amount = $data['total']; 
    $phone = isset($data['phone']) ? $data['phone'] : 'N/A'; 

    // --- DEMO MODE: SKIP SAFARICOM ---
    if ($payment_method === "M-Pesa") {
        // We just pretend we sent the prompt
        $demo_msg = " [DEMO MODE] M-Pesa Prompt sent to " . $phone;
    } else {
        $demo_msg = " Payment via " . $payment_method;
    }

    // --- SAVE TO DATABASE & UPDATE STOCK ---
    foreach ($cart as $item) {
        // Use ?? to prevent "Undefined index" errors if keys are missing
        $product_id = isset($item['id']) ? $item['id'] : 0;
        $qty = isset($item['qty']) ? $item['qty'] : 0;
        $item_total = isset($item['total']) ? $item['total'] : 0;

        if ($product_id > 0) {
            // 1. Record the sale (Make sure your column is named 'quantity')
            $query = "INSERT INTO sales (product_id, quantity, total_amount, payment_method, sale_date) 
                      VALUES ('$product_id', '$qty', '$item_total', '$payment_method', NOW())";
            mysqli_query($conn, $query);

            // 2. Update the stock levels
            $update_stock = "UPDATE products SET quantity = quantity - $qty WHERE id = '$product_id'";
            mysqli_query($conn, $update_stock);
        }
    }

    echo "Transaction Successful!" . $demo_msg . ". Database & Stock Updated.";
} else {
    echo "Error: No data received.";
}
?>