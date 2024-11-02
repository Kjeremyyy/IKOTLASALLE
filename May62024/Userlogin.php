<?php
if (isset($_POST['submit'])) {
    $IDNumber = $_POST['USERID'];
    $PASSWORD = $_POST['PASSWORD'];
    $location = $_POST['location']; // Store the location value

    $serverName = "localhost";
    $databaseName = "id22120907_ikottracker";
    $username = "id22120907_ikotlasalletracker";
    $password = "Ikottracker123!";
    $conn = mysqli_connect($serverName, $username, $password, $databaseName);

    if ($conn === false) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // First, check if the UserID exists in the database
    $checkIDSql = "SELECT USERID FROM PASSENGERREG WHERE USERID = ?";
    $stmt = mysqli_prepare($conn, $checkIDSql);  // Use prepared statement
    mysqli_stmt_bind_param($stmt, "s", $IDNumber);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result === false) {
        die("Error checking USERID: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) === 0) {
        echo "<script>alert('USERID does not exist');window.location = 'index.html';</script>";
    } else {
        // UserID exists, now check if the password is correct
        $sql = "SELECT USERID, PASSWORD FROM PASSENGERREG WHERE USERID = ? AND PASSWORD = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $IDNumber, $PASSWORD);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result === false) {
            die("Error checking password: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) === 0) {
            echo "<script>alert('Incorrect Password');window.location = 'index.html';</script>";
        } else {
            // Authentication successful
            session_start();
            $_SESSION['loggedInUserId'] = $IDNumber;
            $_SESSION['location'] = $location; // Store the location value in session
            if (!empty($_POST['qrValue'])) {
                $_SESSION['qrCodeValue'] = $_POST['qrValue']; 
            }
            header("Location: Userpage.php");
            exit();
        }
    }

    mysqli_stmt_close($stmt);  // Close the prepared statement
    mysqli_close($conn);
}
?>