<?php

include('cors.php');
include('../database.php');

// Retrieve the username and password from the POST request
//$username = $_POST['username'];
//$password = $_POST['password'];

// Encrypt the password using MD5
//$encryptedPassword = md5($password);

// Query the database to check if credentials match
$query = "SELECT * FROM products WHERE id=1";
$stmt = $conn->prepare($query);
//$stmt->bindParam(':username', $username);
//$stmt->bindParam(':password', $encryptedPassword);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // Valid credentials
    $response = [
        'success' => true,
        'message' => 'Login successful',
        'pro_name' => $product['pro_name'],
        'pro_rp' => $product['pro_rp'],
        'pro_sp' => $product['pro_sp'],
        'pro_desc' => $product['pro_desc'],
        'pro_img_1' => $product['pro_img_1'],
        'pro_img_2' => $product['pro_img_2'],
        'pro_img_3' => $product['pro_img_3'],
    ];
} else {
    // Invalid credentials
    $response = [
        'success' => false,
        'message' => 'Invalid username or password',
    ];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>