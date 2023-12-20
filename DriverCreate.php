<?php
$adminnameErr = '';
$adminLicensedErr = '';
$adminUSERNAMEErr = '';
$adminPASSWORD = $_POST['adminPASSWORD'];
$adminretypepassword = $_POST['adminRETYPEPASSWORD']; 

if (empty($_POST['adminNAME'])) {
    $adminnameErr = 'Name is required';
} else {
    $adminNAME = $_POST['adminNAME'];
}

if (empty($_POST['adminLicensed'])) {
    $adminLicensedErr = 'LICENSED_NO. is required';
} else {
    $adminLicensed = $_POST['adminLicensed']; 
}

if (empty($_POST['adminUSERNAME'])) {
    $adminUSERNAMEErr = 'USERNAME is required';
} else {
    $adminUSERNAME = $_POST['adminUSERNAME']; 
}

if ($adminPASSWORD == $adminretypepassword) {
    $adminpassworderr = "Your password does not match";
    
    $serverName = "KARL\SQLEXPRESS";
    $connectionOptions = [
        "Database" => "WEBAPP",
        "Uid" => "",    
        "PWD" => ""
    ];
    
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    
    if ($conn == false) {
        die(print_r(sqlsrv_errors(), true));
    } 

    
    
    // Check if LICENSED_NO already exists
    $checkSql = "SELECT COUNT(*) AS count FROM DRIVERREG WHERE LICENSED_NO = '$adminLicensed'";
    $checkResult = sqlsrv_query($conn, $checkSql);
    
    if ($checkResult) {
        $row = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
        $count = $row['count'];
        
        if ($count > 0) {
            echo '<script type="text/javascript">alert("Licensed number already exists"); window.location.href = "createaccount.html";</script>';
            
            
        } else {    
            // LICENSED_NO doesn't exist, proceed with the insert
            $sql = "INSERT INTO DRIVERREG (LICENSED_NO, NAME, USERNAME, PASSWORD) VALUES ('$adminLicensed', '$adminNAME', '$adminUSERNAME', '$adminPASSWORD')";
            $results = sqlsrv_query($conn, $sql);

            if ($results) {
                header("Location: index.html");
                exit();
                echo 'Registration Successful';
            } else {
                echo 'Error';
            }
        }
    } else {
        echo 'Error checking LICENSED_NO existence';
    }
} else {
    $adminpassworderr = "Password does not match";
    echo $adminpassworderr;
}
?>
