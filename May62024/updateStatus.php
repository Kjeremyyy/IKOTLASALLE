<?php
header("Access-Control-Allow-Origin: *");

$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

// Establish MySQL connection
$conn = mysqli_connect($serverName, $username, $password, $databaseName);

if ($conn === false) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newStatus'])) {
    $newStatus = $_POST['newStatus'];

    // Insert the status change into the 'STATUS' table
    $insertHistorySql = "INSERT INTO STATUS (STATUS, TIME) VALUES (?, NOW())";  // Use NOW() for MySQL

    // Use prepared statements for security
    $stmt = mysqli_prepare($conn, $insertHistorySql);
    mysqli_stmt_bind_param($stmt, "s", $newStatus);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Insertion successful
        echo json_encode(['success' => true]);
    } else {
        // Handle insert failure
        echo json_encode(['success' => false, 'error' => 'Insert failed']);
    }

    mysqli_stmt_close($stmt);
}

// Close the connection
mysqli_close($conn);
?>
