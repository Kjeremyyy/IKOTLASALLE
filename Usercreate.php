<?php
$USERIDErr = '';
$NAMEErr = '';
$usernameErr = '';
$passwordErr = '';
$retypePasswordErr = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $USERID = $_POST['USERID'];
    $NAME = $_POST['NAME'];
    $USERNAME = $_POST['USERNAME'];
    $PASSWORD = $_POST['PASSWORD'];
    $retypepassword = $_POST['RETYPEPASSWORD'];

    if (empty($USERID)) {
        $USERIDErr = 'USERID is required';
        echo $USERIDErr;
    }

    if (empty($NAME)) {
        $NAMEErr = 'FULLNAME is required';
        echo $USERIDErr;
    }

    if (empty($USERNAME)) {
        $usernameErr = 'Username is required';
        echo $usernameErr;
    }

    if (empty($PASSWORD)) {
        $passwordErr = 'Password is required';
        echo $passwordErr;
    }

    if (empty($retypepassword)) {
        $retypePasswordErr = 'Retype Password is required';
        echo $retypePasswordErr;
    }

    if ($PASSWORD != $retypepassword) {
        $passwordErr = 'Passwords do not match';
        echo $passwordErr;
    }

    if (empty($USERIDErr) && empty($NAMEErr) && empty($usernameErr) && empty($passwordErr) && empty($retypePasswordErr)) {
        $serverName = "KARL\SQLEXPRESS";
        $connectionOptions = [
            "Database" => "WEBAPP",
            "Uid" => "",    
            "PWD" => ""
        ];
        $conn = sqlsrv_connect($serverName, $connectionOptions);

        if ($conn == false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            echo 'Connection Success' . "<br>";
        }

        // Check if USERID already exists
        $checkSql = "SELECT COUNT(*) AS count FROM PASSENGERREG WHERE USERID = '$USERID'";
        $checkResult = sqlsrv_query($conn, $checkSql);

        if ($checkResult) {
            $row = sqlsrv_fetch_array($checkResult, SQLSRV_FETCH_ASSOC);
            $count = $row['count'];

            if ($count > 0) {
                echo '<script type="text/javascript">
                        alert("USERID already exists");
                        window.location.href = "Createaccount.html"; 
                      </script>';
            } else {
                // USERID doesn't exist, proceed with the insert
                $sql = "INSERT INTO PASSENGERREG (USERID, NAME, USERNAME, PASSWORD,BALANCED) VALUES ('$USERID', '$NAME', '$USERNAME', '$PASSWORD','500')";
                $results = sqlsrv_query($conn, $sql);

                if ($results) {
                    header("Location: index.html ");
                    echo 'Registration Successful';
                } else {
                    echo 'Error';
                }
            }
        } else {
            echo 'Error checking USERID existence';
        }
    }
}
?>
