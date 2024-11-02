<?php

// Start the session
session_start();

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

// Define the secret code
$secretCode = "@dm!n123"; // Change this to your desired secret code

// Check if the form is submitted
if(isset($_POST['adminlogin'])) {
    // Check if the secret code matches
    if($_POST['secret_code'] == $secretCode) {
        // If the secret code matches, set session variable and redirect to adminpage.php
        $_SESSION['admin_logged_in'] = true;
        header("Location: adminpage.php");
        exit;
    } else {
        echo '<script type="text/javascript">alert("WrongCode"); window.location.href = "index.html";</script>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Log In</title>
    <style>
        body {
            background-image: url(images/dlsud.png);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(45, 45, 45, 0.5); /* Adjust the tint color and opacity as needed */
            z-index: -1;
        }

        .admin-form {
            position: relative;
            background: transparent;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.8);
            padding: 20px;
            max-width: 400px;
            width: auto;
            margin: auto;
            z-index: 1;
        }

        .admin-form::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: rgba(187, 184, 184, 0.8); /* Adjust the overlay opacity as needed */
            border-radius: 30px;
            z-index: -1;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: black;
            padding: 5px;
        }

        input {
            width: 80%;
            display: block;
            margin: 0 auto; /* Updated property */
            padding: 10px 15px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: rgb(224, 251, 242);
            font-weight: bold;
        }

        button {
            background-color: rgb(4, 88, 83);
            color: #fff;
            font-size: 12px;
            padding: 10px 15px;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
        }

        button.submit {
            background-color: rgb(4, 88, 83);
        }

        @media (max-width: 768px) {
            .admin-form {
                width: auto;
                max-width: none; /* Remove the maximum width for smaller screens */
            }
        }
    </style>
</head>
<body>

<form method="POST" action="" class="admin-form">
    <label for="secret_code">Enter Admin Code:</label><br>
    <input type="password" id="secret_code" name="secret_code" required><br><br>
    <button type="submit" name="adminlogin">Log In</button>
</form>

</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>
