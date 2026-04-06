<?php
// Set header to receive JSON
header("Content-Type: application/json");

// 1. Read the raw data from Safaricom
$stkCallbackResponse = file_get_contents('php://input');

// 2. Log the response to a text file (Very helpful for debugging on XAMPP!)
$logFile = "MpesaResponse.txt";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse . PHP_EOL);
fclose($log);

// 3. Decode the JSON
$data = json_decode($stkCallbackResponse);

$resultCode = $data->Body->stkCallback->ResultCode;
$resultDesc = $data->Body->stkCallback->ResultDesc;
$merchantRequestID = $data->Body->stkCallback->MerchantRequestID;
$checkoutRequestID = $data->Body->stkCallback->CheckoutRequestID;

// 4. Check if payment was successful
if ($resultCode == 0) {
    // Payment was successful (ResultCode 0)
    $callbackMetadata = $data->Body->stkCallback->CallbackMetadata->Item;
    
    // Extract specific details from the metadata array
    $amount = $callbackMetadata[0]->Value;
    $mpesaReceiptNumber = $callbackMetadata[1]->Value;
    $transactionDate = $callbackMetadata[3]->Value;
    $phoneNumber = $callbackMetadata[4]->Value;

    // 5. Connect to your DB to record the successful payment
    include('db.php');
    
    // Example: Update a 'payments' table or mark a sale as 'Paid'
    $sql = "INSERT INTO mpesa_transactions (MerchantRequestID, CheckoutRequestID, ResultCode, Amount, MpesaReceiptNumber, PhoneNumber) 
            VALUES ('$merchantRequestID', '$checkoutRequestID', '$resultCode', '$amount', '$mpesaReceiptNumber', '$phoneNumber')";
    
    mysqli_query($conn, $sql);
}

// Safaricom expects a response to stop sending the callback
echo json_encode(["ResultCode" => 0, "ResultDesc" => "Success"]);
?>