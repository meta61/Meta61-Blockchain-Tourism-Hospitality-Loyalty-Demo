<?php
//session_start();

// Include config file
require_once "config.php";
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Include aoi configuration
require_once "log.php";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['create'])) {
    $name = $_POST['name'];
    //$amount= $_POST['amount'];
    $description = $_POST['description'];
    $sql = "INSERT INTO taxis (name,description) VALUES ('$name', '$description')";
    $conn->query($sql);

    //API Login
    $_SESSION['registerResponse'] = registerUser($_SESSION["username"], "Org1", "$name");
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM taxis WHERE id=$id";
    $conn->query($sql);
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['uname'];
    //$amount= $_POST['uamount'];
    $description = $_POST['udescription'];
    $sql = "UPDATE taxis SET name='$name', description='$description' WHERE id=$id";
    $conn->query($sql);

    //API Login
    $_SESSION['registerResponse'] = registerUser($_SESSION["username"], "Org1", "$name");
}

$result = $conn->query("SELECT * FROM taxis");

$currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//echo "The current location is: $currentUrl";

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

        .heading {
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

        .heading2 {
            display: none;
        }

        .content2 {
            display: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="heading">
    <a  href="<?php echo dirname($currentUrl) ?>/home.php" ><h3>Reward</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/hotel.php"><h3>Hotel</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/taxi.php"><h3>Taxi</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/customer.php"><h3>Customer</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/redeem.php"><h3>Redeem</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/log.php"><h3>Logs</h3></a>
    <a  href="<?php echo dirname($currentUrl) ?>/logout.php"><h3>Log out</h3></a>
    </div>
    <div class="heading">
        <h2>Taxis</h2>
        <a id="newlink" href="#"><h3>New</h3></a>
    </div>

    <table>
        <tr>
            <th>Name</th>
            <!-- <th>Amount</th> -->
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <!-- <td><?= $row['amount'] ?></td> -->
                <td><?= $row['description'] ?></td>
                <td>
                    <!-- <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a> -->
                    <a id="update_<?= $row['id'] ?>" href="?edit=<?= $row['id'] ?>">Edit</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <div id="divCreateForm" style="display: none;">
        <h2>Create Item</h2>
        <form method="post" id="CreateForm"  autocomplete="off">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" required placeholder="Enter name"  autocomplete="off"><br>
            <!-- <label for="amount">Amount:</label><br>
            <input type="text" id="amount" name="amount" required placeholder="Enter amount"  autocomplete="off"><br> -->
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required placeholder="Enter description"  autocomplete="off"></textarea><br>
            <button type="submit" name="create">Create</button>
        </form>
    </div>

    <div id="divUpdateForm" >
    <?php if (isset($_GET['edit'])) {
        $id = $_GET['edit'];
        $result = $conn->query("SELECT * FROM taxis WHERE id=$id");
        $row = $result->fetch_assoc(); ?>
        <h2>Edit Item</h2>
        <form method="post" id="UpdateForm" autocomplete="off">
            <input type="hidden" name="id" value="<?= $row['id'] ?>" autocomplete="off">
            <label for="uname">Name:</label><br>
            <input type="text" id="uname" name="uname" value="<?= $row['name'] ?>" required placeholder="Enter name"  autocomplete="off"><br>
            <!-- <label for="uamount">Amount:</label><br>
            <input type="text" id="uamount" name="uamount" value="<?= $row['amount'] ?>" required placeholder="Enter amount"  autocomplete="off"><br> -->
            <label for="udescription">Description:</label><br>
            <textarea id="udescription" name="udescription" required placeholder="Enter description"  autocomplete="off"><?= $row['description'] ?></textarea><br>
            <button type="submit" name="update">Update</button>
        </form>
    <?php } ?>
</div>

</div>

<script type="text/javascript">
    document.getElementById("newlink").onclick = function() {
        document.getElementById("divUpdateForm").style.display = 'none';
        document.getElementById("divCreateForm").style.display = 'block';
    }

    var divUpdateForms = document.querySelectorAll("[id^=update_]");
    divUpdateForms.forEach(function(element) {
        element.onclick = function() {
            document.getElementById("divCreateForm").style.display = 'none';
            document.getElementById("divUpdateForm").style.display = 'block';
        }
    });
</script>

</body>
</html>

<?php
// Close connection
$conn->close();
?>
