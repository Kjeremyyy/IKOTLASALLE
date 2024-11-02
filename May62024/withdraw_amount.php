<?php
$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

// Create connection
$conn = mysqli_connect($serverName, $username, $password, $databaseName);

// Check connection
if ($conn === false) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve input data
$licensedNumber = $_POST['licensed_number'];
$withdrawalAmount = $_POST['withdrawal_amount'];

// Validate input data (you may add more validation as needed)

// Check if wallet balance is sufficient for withdrawal
$sqlCheckBalance = "SELECT Wallet FROM DRIVERREG WHERE LICENSED_NUMBER = '$licensedNumber'";
$result = mysqli_query($conn, $sqlCheckBalance);
$row = mysqli_fetch_assoc($result);
$walletBalance = $row['Wallet'];

if ($walletBalance < $withdrawalAmount) {
    echo '<script type="text/javascript">alert("Insufficient Balance"); window.location.href = "adminpage.php";</script>';
    exit; // Stop execution if balance is insufficient
}

// Deduct withdrawal amount from driver's wallet
$sql = "UPDATE DRIVERREG SET Wallet = Wallet - $withdrawalAmount WHERE LICENSED_NUMBER = '$licensedNumber'";

if (mysqli_query($conn, $sql)) {
    // Log the transaction
    $transactionNumber = null; // You can leave this null, assuming it's auto-incremented
    $time = date("Y-m-d H:i:s"); // Get current time

    $logSql = "INSERT INTO DRIVERTRANS ( Licensed_Number, Withdrawal, Time) 
               VALUES ( '$licensedNumber', '$withdrawalAmount', '$time')";
    
    if (mysqli_query($conn, $logSql)) {
        echo '<script type="text/javascript">alert("Successful Withdrawal"); window.location.href = "adminpage.php";</script>';
    } else {
        echo "Error logging transaction: " . mysqli_error($conn);
    }
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
