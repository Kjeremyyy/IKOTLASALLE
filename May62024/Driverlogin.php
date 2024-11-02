<?php
session_start();



$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

$conn = mysqli_connect($serverName, $username, $password, $databaseName);

if ($conn === false) {
    die("Connection failed: " . mysqli_connect_error());
}

$USERNAME = $_POST['adminLicensed'];
$PASSWORD = $_POST['adminPASSWORD'];

$sql = "SELECT LICENSED_NUMBER, PASSWORD FROM DRIVERREG WHERE LICENSED_NUMBER = ? AND PASSWORD = ?";
$stmt = mysqli_prepare($conn, $sql);  // Use prepared statement for security
mysqli_stmt_bind_param($stmt, "ss", $USERNAME, $PASSWORD);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result === false) {
    die("Query failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

if ($row) {
    // Insert clock details (can be adjusted as per your application logic)
    $insertClockSql = "INSERT INTO DRIVER(LICENSED_NUMBER, TIME_IN, TIME_OUT) VALUES (?, CURTIME(), NULL)";

    $stmt = mysqli_prepare($conn, $insertClockSql);
    mysqli_stmt_bind_param($stmt, "s", $USERNAME);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) !== 1) {
        die("Insert query failed: " . mysqli_error($conn));
    }

    $_SESSION['loggedin'] = true;
    header("Location: Driverpage.php");
    exit();
} else {
    // Check if the username exists in the database
    $checkIDSql = "SELECT LICENSED_NUMBER FROM DRIVERREG WHERE LICENSED_NUMBER = ?";
    $stmt = mysqli_prepare($conn, $checkIDSql);
    mysqli_stmt_bind_param($stmt, "s", $USERNAME);
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);

    if ($checkResult === false) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($checkResult) > 0) {
        // Username is correct, but password is incorrect
        echo "<script>alert('Incorrect Password');window.location = 'index.html';</script>";
        exit(); // Ensure script execution stops here
    } else {
        // Username does not exist
        echo "<script>alert('Licensed No.# Registered does not exist');window.location = 'index.html';</script>";
        exit(); // Ensure script execution stops here
    }
}

mysqli_close($conn);
?>
