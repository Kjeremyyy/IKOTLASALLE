<?php

if (isset($_POST['submit'])) {
    $IDNumber = $_POST['USERID'];
    $PASSWORD = $_POST['PASSWORD'];

    $serverName = "KARL\SQLEXPRESS";
    $connectionOptions = [
        "Database" => "WEBAPP",
        "Uid" => "", // Add your database username
        "PWD" => "" // Add your database password
    ];

    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }
   
    $sql = "SELECT USERID, PASSWORD FROM PASSENGERREG WHERE USERID = ? AND PASSWORD = ?"; 
    $params = array($IDNumber, $PASSWORD);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row) {
        session_start();
        $_SESSION['loggedInUserId'] = $IDNumber;
        header("Location: Userpage.php");
        exit();
       
    } else {
        $checkIDSql = "SELECT USERID FROM PASSENGERREG WHERE USERID = ?";
        $checkUsernameQuery = sqlsrv_query($conn, $checkIDSql, array($IDNumber));

        if ($checkUsernameQuery === false) {
           
            die(print_r(sqlsrv_errors(), true));
        }
        
        if (sqlsrv_has_rows($checkUsernameQuery)) {
            echo "<script>alert('Incorrect Password');window.location = 'index.html';</script>";
        } else {
            echo "<script>alert('USERID does not exist');window.location = 'index.html';</script>";
        }
        
    }

    sqlsrv_close($conn);   
}

?>
