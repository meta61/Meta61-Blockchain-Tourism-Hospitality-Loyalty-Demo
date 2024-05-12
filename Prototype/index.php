<?php
session_start();

// Initialize an array to store API responses
$apiResponses = isset($_SESSION['apiResponses']) ? $_SESSION['apiResponses'] : [];

// Function to register user
function registerUser($username, $orgName, $role) {
    $url = 'https://sandbox.bc.meta61.com.au/users';
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
    $url = 'https://sandbox.bc.meta61.com.au/channels/mychannel/chaincodes/fabcar';
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
        'Authorization: Bearer ' . $token
    );
    return sendCurlRequest($url, $data, $headers);
}

// Function to get history for asset
function getAssetHistory($assetId, $token) {
    $url = 'https://sandbox.bc.meta61.com.au/channels/mychannel/chaincodes/fabcar';
    $data = array(
        "fcn" => "getHistoryForAsset",
        "peers" => ["peer0.org1.meta61.com.au", "peer0.org2.meta61.com.au"],
        "chaincodeName" => "fabcar",
        "channelName" => "mychannel",
        "args" => [$assetId]
    );
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
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
    $response = curl_exec($ch);
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
<html>
<head>
    <title>Meta61 Blockchain Demo</title>
    <style>
        body {
            background-color: #f2f2f2; /* Light gray background color */
            font-family: Arial, sans-serif; /* Specify font family */
            margin: 0; /* Remove default margin */
            padding: 20px; /* Add some padding */
        }
        h1 {
            color: #333; /* Dark gray text color */
            text-align: center; /* Center align the title */
        }
        form {
            margin-bottom: 20px; /* Add some space between forms */
        }
        input[type="text"], input[type="submit"], select {
            padding: 8px; /* Add padding to text fields and submit buttons */
            margin-bottom: 10px; /* Add some space between form elements */
            border: 1px solid #ccc; /* Add border to text fields and submit buttons */
            border-radius: 4px; /* Add border radius */
            width: calc(100% - 18px); /* Set width of text boxes to be same as submit button */
            box-sizing: border-box; /* Include padding and border in the width calculation */
        }
        input[type="submit"] {
            background-color: #4CAF50; /* Green submit button */
            color: white; /* White text color */
            cursor: pointer; /* Change cursor to pointer on hover */
        }
        input[type="submit"]:hover {
            background-color: #45a049; /* Dark green hover color */
        }
        p {
            margin-bottom: 10px; /* Add some space between paragraphs */
        }
    </style>
</head>
<body>

<h1>Meta61 Blockchain Demo</h1>

<h2>Register User</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="username" placeholder="Username"><br>
    <select name="orgName">
        <option value="Org1">Org1</option>
        <!-- Add more options here if needed -->
    </select><br>
    <select name="role">
        <option value="importer">Importer</option>
        <!-- Add more options here if needed -->
    </select><br>
    <input type="submit" value="Register User">
</form>

<h2>Add Car</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="carId" placeholder="Car ID"><br>
    <input type="text" name="make" placeholder="Make"><br>
    <input type="text" name="model" placeholder="Model"><br>
    <input type="text" name="color" placeholder="Color"><br>
    <input type="text" name="owner" placeholder="Owner"><br>
    <input type="submit" value="Add Car">
</form>

<h2>Get Asset History</h2>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="assetId" placeholder="Asset ID"><br>
    <input type="submit" value="Get Asset History">
</form>

<!-- Session clear/reset form -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="clearSession" value="true">
    <input type="submit" value="Clear Session">
</form>

<!-- Print session information button -->
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="printSessionInfo" value="true">
    <input type="submit" value="Print Session Information">
</form>

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
    print_r($_SESSION);
    echo "</pre>";
}
?>
</body>
</html>
