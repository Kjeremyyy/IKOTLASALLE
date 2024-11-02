<?php
header("Access-Control-Allow-Origin: *");
session_start();

// Database connection details (replace with your MySQL credentials)
 $serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

// Establish MySQL connection
$conn = mysqli_connect($serverName, $username, $password, $databaseName);

if ($conn === false) {
    // Consider logging this error instead of displaying it
    error_log(mysqli_connect_error(), true);
    header("Location: errorPage.php"); // Redirect to an error page or similar
    exit;
}

// Check if the jeep location is set and validate it
if (isset($_POST['jeepLocation']) && !empty($_POST['jeepLocation']) && isset($_POST['dropOffSubmit'])) {
    $jeepLocation = mysqli_real_escape_string($conn, $_POST['jeepLocation']); // Escape for security

    // SQL to delete records
   $sql = "DELETE FROM USERPAGE WHERE DESTINATION = '$jeepLocation' AND CURRENT_LOCATION = 'PICK-UP'";

    // Execute query
    $query = mysqli_query($conn, $sql);

    if ($query === false) {
        // Consider logging this error
        error_log(mysqli_error($conn), true);
        header("Location: errorPage.php"); // Redirect to an error page or similar
    } else {
        // Redirect back to the driver page or a confirmation page
       echo '<script type="text/javascript">alert("Successfuly DropOff"); window.location.href = "Driverpage.php";</script>';
    }
} else {
    // Redirect back with an error message or to a specific error page
    header("Location: Driverpage.php?deleteStatus=error");
}

// Close the database connection
mysqli_close($conn);
?>
