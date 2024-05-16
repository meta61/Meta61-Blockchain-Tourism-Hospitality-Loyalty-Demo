<?php
session_start();

// Include config file
require_once "config.php";
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//echo "The current location is: $currentUrl";

$_SESSION['host']= 'http://np-bc-meta61.eastus.cloudapp.azure.com:4000';

// Initialize an array to store API responses
$apiResponses = isset($_SESSION['apiResponses']) ? $_SESSION['apiResponses'] : [];

// Function to register user
function registerUser($username, $orgName, $role) {
    $url = $_SESSION['host'].'/users';
    $data = array(
        'username' => $username,
        'orgName' => $orgName,
        'attrs' => array(
            array(
                'name' => 'abac.role',
                'value' => $role,
                'ecert' => true
            )
        )
    );
    $headers = array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data))
    );
    return sendCurlRequest($url, $data, $headers);
}

// Function to add a car
function addCar($carId, $make, $model, $color, $owner, $token) {
    $url = $_SESSION['host'].'/channels/mychannel/chaincodes/fabcar';
    $data = array(
        "fcn" => "createCar",
        "peers" => ["peer0.org1.example.com", "peer0.org2.example.com"],
        "chaincodeName" => "fabcar",
        "channelName" => "mychannel",
        "args" => [$carId, $make, $model, $color, $owner]
    );
    $headers = array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($data)),
        'Authorization: Bearer ' . $_SESSION["token"]
    );
    return sendCurlRequest($url, $data, $headers);
}

// Function to get history for asset
function getAssetHistory($assetId, $token) {
    $url = $_SESSION['host'].'/channels/mychannel/chaincodes/fabcar';
    $data = array(
        "fcn" => "getHistoryForAsset",
        "peers" => ["peer0.org1.meta61.com.au", "peer0.org2.meta61.com.au"],
        "chaincodeName" => "fabcar",
        "channelName" => "mychannel",
        "args" => [$assetId]
    );
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' .  $_SESSION["token"]
    );
    return sendCurlRequest($url, $data, $headers);
}

// Function to send cURL request
function sendCurlRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Disable SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL certificate verification

    // Get HTTP status code
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    //$response = 200;
    $response = curl_exec($ch);

    // Save request and response to database
if($response !== false){
    // Prepare statement for inserting API request and response into the database
    $sql = "INSERT INTO api_logs (user_id, api_url, http_status, request_data, response_data, created_at) VALUES (?, ?, ?, ?, ?, NOW())";


        $stmt = $GLOBALS["conn"]->prepare($sql);

        // Bind parameters to the prepared statement
        $stmt->bind_param("isiss", $param_user_id, $param_api_url, $param_http_status, $param_request_data, $param_response_data);

        // Set parameters
        $param_user_id = $_SESSION["id"];
        $param_api_url = $url;
        $param_http_status = $http_status;
        $param_request_data = json_encode($data); // You can save request data if needed
        $param_response_data = json_encode($response);

        // Execute the prepared statement
        if(!$stmt->execute()){
            echo "Error: " . $conn->error;
            // You might want to handle the error in a different way
        }

        // Close statement
        $stmt->close();
    }


    curl_close($ch);
    return $response;
}

// Handle session clear/reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clearSession'])) {
    session_unset();
    session_destroy();
    $_SESSION['apiResponses'] = [];
    echo "Session cleared successfully.";
}

// Handle user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['orgName']) && isset($_POST['role'])) {
    $registerResponse = registerUser($_POST['username'], $_POST['orgName'], $_POST['role']);
    $_SESSION['registerResponse'] = $registerResponse;
    $apiResponses[] = "User Registration Response: " . $registerResponse;
}

// Handle adding a car
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['carId']) && isset($_POST['make']) && isset($_POST['model']) && isset($_POST['color']) && isset($_POST['owner'])) {
    if (isset($_SESSION['registerResponse'])) {
        $registerResponseArray = json_decode($_SESSION['registerResponse'], true);
        if (isset($registerResponseArray['token'])) {
            $token = $registerResponseArray['token'];
            $addCarResponse = addCar($_POST['carId'], $_POST['make'], $_POST['model'], $_POST['color'], $_POST['owner'], $token);
            $apiResponses[] = "Add Car Response: " . $addCarResponse;
        } else {
            $apiResponses[] = "Error: User registration token not found.";
        }
    } else {
        $apiResponses[] = "Error: User is not registered. Please register first.";
    }
}

// Handle getting asset history
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assetId'])) {
    if (isset($_SESSION['registerResponse'])) {
        $registerResponseArray = json_decode($_SESSION['registerResponse'], true);
        if (isset($registerResponseArray['token'])) {
            $token = $registerResponseArray['token'];
            $assetHistoryResponse = getAssetHistory($_POST['assetId'], $token);
            $apiResponses[] = "Asset History Response: " . $assetHistoryResponse;
        } else {
            $apiResponses[] = "Error: User registration token not found.";
        }
    } else {
        $apiResponses[] = "Error: User is not registered. Please register first.";
    }
}

// Save API responses in session
$_SESSION['apiResponses'] = $apiResponses;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meta61-Loyalty</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .heading2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .heading h2 {
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">
<div class="heading2">
    <a  href="<?php echo dirname($currentUrl) ?>/home.php" ><h3>Reward</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/hotel.php"><h3>Hotel</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/taxi.php"><h3>Taxi</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/customer.php"><h3>Customer</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/redeem.php"><h3>Redeem</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/log.php"><h3>Logs</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/logout.php"><h3>Log out</h3></a>
    </div>

<div class="content2" style="width: 800px;">

<div  style="width: 400px; float:left;">
<!-- Print session information button -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="printSessionInfo" value="true">
    <button type="submit" value="Print Session Information">Print Session Information</button>
</form>
</div>
<div style="width: 400px; float:left;">
<!-- Session clear/reset form -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="clearSession" value="true">
    <button type="submit" value="Clear Session">Clear Session</button>
</form>
</div>

<div>
<!-- Print API responses -->
<h2>API Responses</h2>
<?php
foreach ($apiResponses as $response) {
    echo "<p>$response</p>";
}

// Print session information if requested
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['printSessionInfo'])) {
    echo "<h2>Session Information</h2>";
    echo "<pre>";
    //print_r($_SESSION);
    //print_r($_SESSION['registerResponse']);
    //$jresponse = json_decode( $_SESSION['registerResponse'], true);
   // $_SESSION["token"] = $jresponse['token'];
    //print_r($jresponse['token']);
    echo "</pre>";
    ?>
    <table>
        <tr>
            <th>Date</th>
            <th>URL</th>
            <th>Request_data</th>
            <th>Response_data</th>            
        </tr>
        <?php 
        $result = $conn->query("SELECT * FROM api_logs ORDER BY id DESC LIMIT 10");
        while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['created_at'] ?></td>
                <td><?= $row['api_url'] ?></td>
                <td><?= $row['request_data'] ?></td>
                <td><?= $row['response_data'] ?></td>              
                
            </tr>
        <?php } ?>
    </table>
    <?php        

}
?>
</div>
</div>
</body>
</html>
