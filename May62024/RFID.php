<?php

$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

$conn = mysqli_connect($serverName, $username, $password, $databaseName);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag'])) {
    // Get the tag and custom data from the POST request
    $tag = $_POST['tag'];
    $customData = isset($_POST['customData']) ? $_POST['customData'] : null;

    // Debugging: Output received POST data
    echo "Received POST Data: ";
    var_dump($_POST);

    // Debugging: Output received data
    echo "Received Tag: " . $tag . "\n";
    echo "Received Custom Data: " . $customData . "\n";

    // Check if the tag value contains the specified substring before inserting
    if (strpos($tag, "bfd656f6169693979de5ebf9") === false) {
        die("Tag value does not meet the condition");
    }

    // Prepare and execute SQL query
    $sql = "INSERT INTO rfid (tag, location, time) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);

    // Check for SQL errors
    if (!$stmt) {
        die("Error in preparing statement: " . mysqli_error($conn));
    }

    // Bind parameters and execute query
    mysqli_stmt_bind_param($stmt, "ss", $tag, $customData);
    $result = mysqli_stmt_execute($stmt);

    // Check for execution errors
    if (!$result) {
        die("Error in executing query: " . mysqli_error($conn));
    }

    // Inform that the record was inserted
    echo "New record created successfully";

    // Free the statement
    mysqli_stmt_close($stmt);
} else {
    // Handle GET requests if needed
    echo "No POST data received";
}

// Close the connection
mysqli_close($conn);
?>
