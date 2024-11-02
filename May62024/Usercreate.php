<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
        echo '<script type="text/javascript">
                        alert("Password does not match");
                        window.location.href = "Createaccount.html"; 
                      </script>';
    }

    if (empty($USERIDErr) && empty($NAMEErr) && empty($usernameErr) && empty($passwordErr) && empty($retypePasswordErr)) {
      $serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";
        $conn = mysqli_connect($serverName, $username, $password, $databaseName);

        if ($conn === false) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Check if USERID already exists
        $checkSql = "SELECT COUNT(*) AS count FROM PASSENGERREG WHERE USERID = ?";
        $stmt = mysqli_prepare($conn, $checkSql);  // Use prepared statement
        mysqli_stmt_bind_param($stmt, "s", $USERID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $count = $row['count'];

            if ($count > 0) {
                echo '<script type="text/javascript">
                        alert("USERID already exists");
                        window.location.href = "Createaccount.html"; 
                      </script>';
            } else {
                // USERID doesn't exist, proceed with the insert
                $sql = "INSERT INTO PASSENGERREG (USERID, NAME, USERNAME, PASSWORD, BALANCED) VALUES (?, ?, ?, ?, 10)";
                $stmt = mysqli_prepare($conn, $sql);  // Use prepared statement
                mysqli_stmt_bind_param($stmt, "ssss", $USERID, $NAME, $USERNAME, $PASSWORD);
                mysqli_stmt_execute($stmt);

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    // Registration Successful
                    // Close database connection
                    mysqli_stmt_close($stmt);
                    mysqli_close($conn);
                    
                    // Redirect after outputting success message
                    header("Location: https://ikotlasalle.000webhostapp.com/index.html");
                    exit(); 
                } else {
                    $errorMessage = 'Error inserting record';
                }
            }
        } else {
            $errorMessage = 'Error checking USERID existence'; // Debugging
        }

        mysqli_stmt_close($stmt);  // Close the prepared statement
        mysqli_close($conn);
    }
}
?>
