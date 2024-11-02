<?php

session_start();

$CURRENTErr = '';
$DESTINATIONErr = '';
$CURRENT_LOCATION = '';
$DESTINATION = '';
$SEATS = '';
$updatedBalance = 0;
$latestJeepLocation = 'Unknown';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['location'])) {
        $CURRENTErr = 'Select your Location';
        echo "<script>alert('Location is needed');window.location = 'Userpage.php';</script>";
        exit();
    } else {
        $CURRENT_LOCATION = $_POST['location'];
    }

    if (empty($_POST['destination'])) {
        $DESTINATIONErr = 'Select your Destination';
    } else {
        $DESTINATION = $_POST['destination'];
    }

    $SEATS = $_POST['seats'];

    $serverName = "localhost";
    $databaseName = "id22120907_ikottracker";
    $username = "id22120907_ikotlasalletracker";
    $password = "Ikottracker123!";
    $conn = mysqli_connect($serverName, $username, $password, $databaseName);

    if ($conn === false) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check user's balance before allowing submission
$loggedInUserId = $_SESSION['loggedInUserId'];
$balanceQuery = "SELECT BALANCED FROM PASSENGERREG WHERE USERID = ?";
$stmt = mysqli_prepare($conn, $balanceQuery);
mysqli_stmt_bind_param($stmt, "s", $loggedInUserId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userBalance = 0;

if ($row = mysqli_fetch_assoc($result)) {
    $userBalance = $row['BALANCED'];
}

// Check if balance is sufficient before allowing submission
if ($userBalance < 10) {
    echo "<script>alert('Insufficient balance to proceed.');window.location = 'Userpage.php';</script>";
    exit();
} else {
    // Deduct balance by 10
    $updateBalanceSql = "UPDATE PASSENGERREG SET BALANCED = BALANCED - 10 WHERE USERID = ?";
    $stmt = mysqli_prepare($conn, $updateBalanceSql);
    mysqli_stmt_bind_param($stmt, "s", $loggedInUserId);
    mysqli_stmt_execute($stmt);
}

    // Fetch the new balance
    $balanceQuery = "SELECT BALANCED FROM PASSENGERREG WHERE USERID = ?";
    $stmt = mysqli_prepare($conn, $balanceQuery);
    mysqli_stmt_bind_param($stmt, "s", $loggedInUserId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $updatedBalance = ($row = mysqli_fetch_assoc($result)) ? $row['BALANCED'] : 0;

    // Insert user page data
    $sql = "INSERT INTO USERPAGE (CURRENT_LOCATION, DESTINATION, SEATS) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $CURRENT_LOCATION, $DESTINATION, $SEATS);
    mysqli_stmt_execute($stmt);

    // Insert into UserLogs
    $insertUserLogSql = "INSERT INTO UserLogs (USERID, Location, time) VALUES (?, ?, NOW())"; // Using MySQL NOW() function to get current time
    $stmt = mysqli_prepare($conn, $insertUserLogSql);
    mysqli_stmt_bind_param($stmt, "ss", $loggedInUserId, $CURRENT_LOCATION);
    mysqli_stmt_execute($stmt);


    // Insert into TransactionHistory
    $insertTransactionSql = "INSERT INTO Transactions (Transaction_number, USERID, Location, Destination, Fare, time) VALUES (NULL, ?, ?, ?, 10, NOW())";
    $stmt = mysqli_prepare($conn, $insertTransactionSql);
    mysqli_stmt_bind_param($stmt, "sss", $loggedInUserId, $CURRENT_LOCATION, $DESTINATION);
    mysqli_stmt_execute($stmt);


    // Fetch user's information based on USERID
    $loggedInUserId = $_SESSION['loggedInUserId'];
    $userInfoQuery = "SELECT Location, Destination, time FROM Transactions WHERE USERID = ? ORDER BY time DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $userInfoQuery);
    mysqli_stmt_bind_param($stmt, "s", $loggedInUserId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = mysqli_fetch_assoc($result);
    $transactionTime = $userData['time'];


    // Assign user's current location and destination
    $LOCATION2 = $userData['Location'];
    $DESTINATION2 = $userData['Destination'];

    $updateDriverBalanceSql = "UPDATE DRIVERREG 
                            INNER JOIN DRIVER ON DRIVERREG.Licensed_number = DRIVER.Licensed_number
                            SET DRIVERREG.WALLET = DRIVERREG.WALLET + 10 
                            WHERE DRIVER.TIME_IN = (SELECT MAX(TIME_IN) FROM DRIVER)
                            AND DRIVER.TIME_OUT IS NULL";
    $stmt = mysqli_prepare($conn, $updateDriverBalanceSql);
    mysqli_stmt_execute($stmt);

    // Fetch the latest Jeep location
    $jeepLocationSql = "SELECT location FROM rfid ORDER BY time DESC LIMIT 1";
    $jeepLocationQuery = mysqli_query($conn, $jeepLocationSql);
    $latestLocationRow = mysqli_fetch_assoc($jeepLocationQuery);
    $latestJeepLocation = $latestLocationRow ? $latestLocationRow['location'] : 'Unknown';

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<style>
        body {
            background-image: url(images/dlsud.png);
            background-position: center;
            background-repeat: no-repeat;   
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(45, 45, 45, 0.5);
            z-index: -1;
        }

        .container {
            position: relative;
            background: transparent;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.8);
            padding: 20px;
            max-width: 400px;
            width: 1000px;
            margin: auto;
            z-index: 1;
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(187, 184, 184, 0.8);
            border-radius: 30px;
            z-index: -1;
        }

        .top-center,
        .middle-center {
            text-align: center;
        }

        .usertitle h1 {
            color: white;
        }

        .Status {
            margin-top: 20px;
        }

        #userStatusText {
            color: blue;
        }

        #jeep {
            width: 30%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .FillupForm {
            max-width: 400px;
            margin: 0 auto;
            
        text-align: center; /* Center-align the content within the container */
    
        }

        label {
            display: block;
            margin-bottom: 8px;
            color:  White;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 30%; /* Adjusted width to fill the container */
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            background-color: rgb(224, 251, 242);
            border-radius: 4px;
            text-align: center;
        }

        input[type="submit"] {
            background-color: rgb(4, 88, 83);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        h2 {
            color: black;
            font-family: 'Courier New', Courier, monospace;
        }
        #jeepneylocation {
        width: 30%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #ccc;
        background-color: rgb(224, 251, 242);
        border-radius: 4px;
        text-align: center; /* Center-align the text within the input */
        display: inline-block; /* Make it an inline block to center with margin */
        margin: 0 auto; /* Center the input text */
    }

    </style>
</head>
<body>
    <div class="container">
        <h1 class="usertitle top-center">You Are Successfully Registered</h1>

        <div class="FillupForm">
        <label for="jeepneylocation">Jeepney Location</label>
            <input type="text" id="jeepneylocation" name="jeepneylocation" value="<?php echo htmlspecialchars($latestJeepLocation); ?>" readonly><br>
            <label for="currentLocation">Current Location:</label>
<input type="text" id="currentLocation" name="currentLocation" value="<?php echo htmlspecialchars($CURRENT_LOCATION); ?>" readonly><br>

<label for="destination">Destination:</label>
<input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($DESTINATION); ?>" readonly><br>
<label for="transactionTime">Transaction Time:</label>
<input type="text" id="transactionTime" name="transactionTime" value="<?php echo htmlspecialchars($transactionTime); ?>" readonly><br>
            <h2>Your New Balance: <?php echo $updatedBalance; ?></h2>
            
       
        </div>
    </div>
   

   
</body>
</html>

  