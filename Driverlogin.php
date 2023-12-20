<?php
session_start();

$serverName = "KARL\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die("Connection failed: " . print_r(sqlsrv_errors(), true));
}

$USERNAME = $_POST['adminLicensed'];
$PASSWORD = $_POST['adminPASSWORD'];

$sql = "SELECT LICENSED_NO, PASSWORD FROM DRIVERREG WHERE LICENSED_NO = ? AND PASSWORD = ?";
$params = array($USERNAME, $PASSWORD);
$sql1 = sqlsrv_query($conn, $sql, $params);

if ($sql1 === false) {
    die("Query failed: " . print_r(sqlsrv_errors(), true));
}

$row = sqlsrv_fetch_array($sql1, SQLSRV_FETCH_ASSOC);

if ($row) {
     // Insert clock details (can be adjusted as per your application logic)
     $insertClockSql = "INSERT INTO DRIVER(LICENSED_NO, TIME_IN, TIME_OUT) VALUES (?, GETDATE(), NULL)";
     $insertClockParams = array($USERNAME);
     $insertClockQuery = sqlsrv_query($conn, $insertClockSql, $insertClockParams);
 
     if ($insertClockQuery === false) {
         die("Insert query failed: " . print_r(sqlsrv_errors(), true));
     }
    $_SESSION['loggedin'] = true;
    header("Location: Driverpage.php");
    exit();
} else {
    // Check if the username exists in the database
    $checkIDSql = "SELECT LICENSED_NO FROM DRIVERREG WHERE LICENSED_NO = ?";
    $checkParams = array($USERNAME);
    $checkUsernameQuery = sqlsrv_query($conn, $checkIDSql, $checkParams);

    if ($checkUsernameQuery === false) {
        die("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_has_rows($checkUsernameQuery)) {
        // Username is correct, but password is incorrect
        echo "<script>alert('Incorrect Password');window.location = 'index.html';</script>";
        exit(); // Ensure script execution stops here
    } else {
        // Username does not exist
        echo "<script>alert('Licensed No.# Registered does not exist');window.location = 'index.html';</script>";
        exit(); // Ensure script execution stops here
    }

   
}

sqlsrv_close($conn);
?>
