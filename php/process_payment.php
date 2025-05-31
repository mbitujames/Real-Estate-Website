<?php
session_start();
require_once '../data/db.php'; 
$property_id=$_SESSION['property_id'];

$config = array(
    "env"              => "sandbox",
    "BusinessShortCode"=> "174379",
    "key"              => "LXu2ZGQCFSuLXE9R4Ifb9krQIX0fOY5Zr0sjgAHGlwAPSF4R",
    "secret"           => "d0kofqc8IYQp4QQy5Be0vJlBFQv2wwYSrSTsmMtadwISIk3Ew74PUq3HkmbUHjVt",
    "TransactionType"  => "CustomerPayBillOnline",
    "passkey"          => "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919",
    "CallBackURL"      => "https://c932-41-89-18-2.ngrok-free.app/projects/php/callback.php", //When using localhost, Use Ngrok to forward the response to your Localhost
    "AccountReference" => "KREPM LD",
    "TransactionDesc"  => "Property Payment - ID: $property_id",
);


if (isset($_POST['pay-btn'])) {
    $property_id = intval($_POST['property_id']);
    $phone = $_POST['MSISDN'];
    $amount = $_POST['payment_amount'];
    $user_id = $_SESSION['user_id']; 
    $payment_method = 'M-PESA';
    $payment_status = 'pending';
    $payment_date = date('Y-m-d H:i:s');
   
    // Uncomment the below when going live or wanting to pay 4 exact total 
    // $amount = $_SESSION['amount']; 

    $access_token = ($config['env']  == "live") ? "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials" : "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials"; 
    $credentials = base64_encode($config['key'] . ':' . $config['secret']); 

    
    $ch = curl_init($access_token);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic " . $credentials]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response); 
    $token = isset($result->{'access_token'}) ? $result->{'access_token'} : "N/A";

    $timestamp = date("YmdHis");
    $password  = base64_encode($config['BusinessShortCode'] . "" . $config['passkey'] ."". $timestamp);

    $curl_post_data = array( 
        "BusinessShortCode" => $config['BusinessShortCode'],
        "Password" => $password,
        "Timestamp" => $timestamp,
        "TransactionType" => $config['TransactionType'],
        "Amount" => $amount,
        "PartyA" => $phone,
        "PartyB" => $config['BusinessShortCode'],
        "PhoneNumber" => $phone,
        "CallBackURL" => $config['CallBackURL'],
        "AccountReference" => $config['AccountReference'],
        "TransactionDesc" => $config['TransactionDesc'],
    ); 

    $data_string = json_encode($curl_post_data);


    $endpoint = ($config['env'] == "live") ? "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest" : "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest"; 


    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer '.$token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response     = curl_exec($ch);
    curl_close($ch);

    
    $result = json_decode(json_encode(json_decode($response)), true);

    // file_put_contents('tests_log',  $result, FILE_APPEND);
    

    if($result['ResponseCode'] === "0"){         //STK Push request successful

        $MerchantRequestID = $result['MerchantRequestID'];
        $CheckoutRequestID = $result['CheckoutRequestID'];

        // Insert the initial payment record into the database
        $sql = "INSERT INTO payments (property_id, user_id, payment_amount, phone, payment_method, payment_status, payment_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiiisss", $property_id, $user_id, $amount, $phone, $payment_method, $payment_status, $payment_date);
        mysqli_stmt_execute($stmt);
        $_SESSION['payment_id'] = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        header('location: dashboard.php');

    }else{
        echo 'error message found';
    }
}

?>
