<?php
// Start session
session_start();

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: home.php");
    exit;
}

// Include database configuration
require_once "config.php";

// API endpoint URL
$api_url = "https://api.example.com/data";

// Set up cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set authorization header
$headers = [
    'Authorization: Bearer ' . $_SESSION["access_token"] // Replace $_SESSION["access_token"] with your actual access token
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if($response === false){
    echo "Error: " . curl_error($ch);
    // You might want to handle the error in a different way, e.g., redirecting to an error page
}

// Get HTTP status code
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close cURL session
curl_close($ch);

// Save request and response to database
if($response !== false){
    // Prepare statement for inserting API request and response into the database
    $sql = "INSERT INTO api_logs (user_id, api_url, http_status, request_data, response_data, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    
    if($stmt = $mysqli->prepare($sql)){
        // Bind parameters to the prepared statement
        $stmt->bind_param("isiss", $param_user_id, $param_api_url, $param_http_status, $param_request_data, $param_response_data);
        
        // Set parameters
        $param_user_id = $_SESSION["id"];
        $param_api_url = $api_url;
        $param_http_status = $http_status;
        $param_request_data = ''; // You can save request data if needed
        $param_response_data = $response;

        // Execute the prepared statement
        if(!$stmt->execute()){
            echo "Error: " . $stmt->error;
            // You might want to handle the error in a different way
        }
        
        // Close statement
        $stmt->close();
    }
}

// Decode JSON response
$data = json_decode($response, true);

// Handle the response
if($data){
    // Display the data
    foreach($data as $item){
        echo "ID: " . $item['id'] . "<br>";
        echo "Name: " . $item['name'] . "<br>";
        echo "Description: " . $item['description'] . "<br>";
        echo "<hr>";
    }
} else {
    echo "No data available.";
}
?>
