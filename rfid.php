<?php
$serverName = "KARL\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",    // Your database username
    "PWD" => ""     // Your database password
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

// Check if the request is POST (to insert data)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag'])) {
    $tag = $_POST['tag']; // Assume 'tag' is the RFID tag's ID
    $customData = isset($_POST['customData']) ? $_POST['customData'] : null;

    // Prepare and execute SQL query
    $sql = "INSERT INTO RFID (tag, location,time) VALUES (?, ?,GETDATE())";  // Adjusted to include custom_data
    $params = array($tag, $customData);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die("Error in executing query: " . print_r(sqlsrv_errors(), true));
    }
    sqlsrv_free_stmt($stmt); // Free the statement

    echo "New records created successfully"; // Inform that the record was inserted
} else {
    // ... [existing code for handling GET requests]
}

// Close the connection
sqlsrv_close($conn);
?>
