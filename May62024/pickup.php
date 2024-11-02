<?php
session_start(); // Start the session at the top

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

$conn = mysqli_connect($serverName, $username, $password, $databaseName);

if ($conn === false) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if it's an AJAX request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jeepLocation'])) {
    $jeepLocation = $_POST['jeepLocation'];

    // Assuming your table name is RFID, update the records
    $updateSql = "UPDATE USERPAGE SET CURRENT_LOCATION = 'PICK-UP' WHERE CURRENT_LOCATION = ?";
    $stmt = mysqli_prepare($conn, $updateSql);  // Use prepared statement
    mysqli_stmt_bind_param($stmt, "s", $jeepLocation);
    mysqli_stmt_execute($stmt);
     

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Send a JSON response back to the client
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        
        exit;
    } else {
        // Handle update failure (e.g., log error, send appropriate response)
        echo json_encode(['success' => false, 'error' => 'Update failed']);
        exit;
    }
} else {
    // Redirect to an error page or handle the situation accordingly
    header("Location: error.html");
    exit;
}

mysqli_close($conn);
?>
