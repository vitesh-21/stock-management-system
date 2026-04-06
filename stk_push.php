<?php
// 1. STK PUSH SETTINGS (Sandbox Credentials)
$businessShortCode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$consumerKey = 'YOUR_CONSUMER_KEY'; // Get from Safaricom Developer Portal
$consumerSecret = 'YOUR_CONSUMER_SECRET';

// 2. TRANSACTION DETAILS
$phone = '2547XXXXXXXX'; // The phone number to prompt (must start with 254)
$amount = '1'; // Amount in Ksh
$accountRef = 'StockSystem';
$transDesc = 'Payment for goods';

// 3. GENERATE TIMESTAMP & PASSWORD
$timestamp = date('YmdHis');
$password = base64_encode($businessShortCode . $passkey . $timestamp);

// 4. GET ACCESS TOKEN
$headers = ['Content-Type: application/json; charset=utf8'];
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;
curl_close($curl);

// 5. INITIATE STK PUSH
$stk_url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$curl_post_data = array(
    'BusinessShortCode' => $businessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $businessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://yourdomain.com/callback.php', // Must be HTTPS to work
    'AccountReference' => $accountRef,
    'TransactionDesc' => $transDesc
);

$data_string = json_encode($curl_post_data);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stk_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);

echo $curl_response; // This will show if the prompt was sent successfully
?>