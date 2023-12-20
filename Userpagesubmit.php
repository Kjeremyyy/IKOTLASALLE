<?php
session_start();

$CURRENTErr = '';
$DESTINATIONErr = '';
$CURRENT_LOCATION = '';
$DESTINATION = '';
$SEATS = '';
$updatedBalance = 0;
$latestJeepLocation = 'Unknown'; // Default value for Jeep location

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['location'])) {
        $CURRENTErr = 'Select your Location';
        echo "<script>alert('Location is needed');window.location = 'userpage.php';</script>";
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

    $serverName = "KARL\\SQLEXPRESS";
    $connectionOptions = [
        "Database" => "WEBAPP",
        "Uid" => "",
        "PWD" => ""
    ];

    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn == false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Deduct balance by 10
    $loggedInUserId = $_SESSION['loggedInUserId'];
    $updateBalanceSql = "UPDATE PASSENGERREG SET BALANCED = BALANCED - 10 WHERE USERID = ?";
    $params = array($loggedInUserId);
    $balanceResult = sqlsrv_query($conn, $updateBalanceSql, $params);

    if ($balanceResult === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the new balance
    $balanceQuery = "SELECT BALANCED FROM PASSENGERREG WHERE USERID = ?";
    $balanceResult = sqlsrv_query($conn, $balanceQuery, $params);

    if ($balanceResult === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($balanceResult, SQLSRV_FETCH_ASSOC);
    $updatedBalance = $row ? $row['BALANCED'] : 0;

    // Insert user page data
    $sql = "INSERT INTO USERPAGE (CURRENT_LOCATION, DESTINATION, SEATS) VALUES (?, ?, ?)";
    $params = array($CURRENT_LOCATION, $DESTINATION, $SEATS);
    $results = sqlsrv_query($conn, $sql, $params);

    if ($results === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the latest Jeep location
    $jeepLocationSql = "SELECT TOP 1 location FROM RFID ORDER BY time DESC";
    $jeepLocationQuery = sqlsrv_query($conn, $jeepLocationSql);

    if ($jeepLocationQuery === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $jeepLocationRow = sqlsrv_fetch_array($jeepLocationQuery, SQLSRV_FETCH_ASSOC);
    $latestJeepLocation = $jeepLocationRow ? $jeepLocationRow['location'] : 'Unknown';

    sqlsrv_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
<<body>
    <div class="container">
        <h1 class="usertitle top-center">You Are Successfully Registered</h1>

        <div class="FillupForm">
        <label for="jeepneylocation">Jeepney Location</label>
            <input type="text" id="jeepneylocation" name="jeepneylocation" value="<?php echo htmlspecialchars($latestJeepLocation); ?>" readonly><br>
            
            <h2>Your New Balance: <?php echo $updatedBalance; ?></h2>
            
       
        </div>
    </div>
</body>
</html>
