<?php

$serverName = "KARL\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => ""
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newStatus'])) {
    $newStatus = $_POST['newStatus'];

    // Insert the status change into the 'STATUS' table
    $insertHistorySql = "INSERT INTO STATUS (STATUS, TIME) VALUES (?, GETDATE())";
    
    // Use prepared statements to prevent SQL injection
    $params = array($newStatus);
    $insertHistoryQuery = sqlsrv_query($conn, $insertHistorySql, $params);

    if ($insertHistoryQuery === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Respond with success message
    echo json_encode(['success' => true]);
    exit;
}