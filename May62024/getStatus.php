<?php
// Assuming you have a database connection established

// Your MySQL connection credentials (replace with your actual details)
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

// Fetch the latest status from the STATUS table
$sql = "SELECT STATUS FROM STATUS ORDER BY TIME DESC LIMIT 1"; // MySQL equivalent for OFFSET/FETCH NEXT
$query = mysqli_query($conn, $sql);

if ($query === false) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($query) > 0) {
    // Output data of the latest status
    $row = mysqli_fetch_assoc($query);
    $latestStatus = $row["STATUS"];

    // Close the database connection
    mysqli_close($conn);

    // Return the latest status as JSON
    echo json_encode(["status" => $latestStatus]);
} else {
    echo json_encode(["status" => "No status found"]);
}

?>
