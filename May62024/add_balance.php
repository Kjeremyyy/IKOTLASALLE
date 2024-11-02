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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID and amount to add from the form
    $userId = $_POST['user_id'];
    $amountToAdd = $_POST['add_balance'];

    // Update balance in the database
    $sql = "UPDATE PASSENGERREG SET BALANCED = BALANCED + $amountToAdd WHERE USERID = '$userId'";
    if (mysqli_query($conn, $sql)) {
        // Close connection
        mysqli_close($conn);
        // Redirect to the same page
        echo '<script type="text/javascript">alert("Successfuly Added"); window.location.href = "adminpage.php";</script>';
        exit();
    } else {
        // Close connection
        mysqli_close($conn);
        echo '<script>alert("Failed to Add");</script>';
        echo "Error updating balance: " . mysqli_error($conn);
    }
}


?>
