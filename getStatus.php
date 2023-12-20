<?php
// Assuming you have a database connection established

// Your database connection credentials
$serverName = "KARL\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => ""
];

// Create connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Check connection
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the latest status from the STATUS table
$sql = "SELECT STATUS FROM STATUS ORDER BY TIME DESC OFFSET 0 ROWS FETCH NEXT 1 ROWS ONLY";
$query = sqlsrv_query($conn, $sql);

if ($query === false) {
    die(print_r(sqlsrv_errors(), true));
}

if (sqlsrv_has_rows($query)) {
    // Output data of the latest status
    $row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
    $latestStatus = $row["STATUS"];

    // Close the database connection
    sqlsrv_close($conn);

    // Return the latest status as JSON
    echo json_encode(["status" => $latestStatus]);
} else {
    echo json_encode(["status" => "No status found"]);
}

?>