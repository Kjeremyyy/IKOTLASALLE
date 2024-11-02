<?php
$adminnameErr = '';
$adminLicensedErr = '';
$adminPASSWORD = $_POST['adminPASSWORD'];
$adminretypepassword = $_POST['adminRETYPEPASSWORD'];


$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

$conn = mysqli_connect($serverName, $username, $password, $databaseName);

if ($conn === false) {
    die(mysqli_connect_error());
}

// Input validation
if (empty($_POST['adminNAME'])) {
    $adminnameErr = 'Name is required';
} else {
    $adminNAME = mysqli_real_escape_string($conn, $_POST['adminNAME']); // Escape for security
}

if (empty($_POST['adminLicensed'])) {
    $adminLicensedErr = 'LICENSED_NO. is required';
} else {
    $adminLicensed = mysqli_real_escape_string($conn, $_POST['adminLicensed']); // Escape for security
}



// Password validation
if ($adminPASSWORD == $adminretypepassword) {
    // Check if LICENSED_NO already exists
    $checkSql = "SELECT COUNT(*) AS count FROM DRIVERREG WHERE LICENSED_NUMBER = '$adminLicensed'";
    $checkResult = mysqli_query($conn, $checkSql);

    if ($checkResult) {
        $row = mysqli_fetch_assoc($checkResult);
        $count = $row['count'];

        if ($count > 0) {
            echo '<script type="text/javascript">alert("Licensed number already exists"); window.location.href = "createaccount.html";</script>';
        } else {
            // LICENSED_NO doesn't exist, proceed with the insert
            $sql = "INSERT INTO DRIVERREG (LICENSED_NUMBER, NAME, PASSWORD,Wallet) VALUES ('$adminLicensed', '$adminNAME',  '$adminPASSWORD','0')";
            $results = mysqli_query($conn, $sql);

            if ($results) {
                header("Location: index.html");
                exit();
            } else {
                echo 'Error';
            }
        }
    } else {
        echo 'Error checking LICENSED_NO existence';
    }

    mysqli_close($conn);
} else {
    $adminpassworderr = "Password does not match";
    echo $adminpassworderr;
}
?>
