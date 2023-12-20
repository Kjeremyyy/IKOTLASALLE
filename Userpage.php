<?php
session_start(); // Ensure you have this at the start

// Check if the user is logged in
if (!isset($_SESSION['loggedInUserId'])) {
    // Redirect to login page if not logged in
    header('Location: index.html');
    exit(); 
}

$loggedInUserId = $_SESSION['loggedInUserId'];


$serverName = "KARL\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => "" 
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT USERNAME FROM PASSENGERREG WHERE USERID = ?";
$params = array($loggedInUserId);
$query = sqlsrv_query($conn, $sql, $params);

if ($query === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the username
$row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
if ($row) {
    $loggedInUserName = $row['USERNAME']; // Fetch the username

} else {
    $loggedInUserName = "Unknown User"; // Fallback username
   
}

// Fetch the user's balance
$balanceQuery = sqlsrv_query($conn, "SELECT BALANCED FROM PASSENGERREG WHERE USERNAME = ?", array($loggedInUserName));
$balanceRow = sqlsrv_fetch_array($balanceQuery, SQLSRV_FETCH_ASSOC);
$userBalance = $balanceRow ? $balanceRow['BALANCED'] : 0; // Default balance to 0 if not found

function getLatestRfidValue() {
    global $conn;

    $sql1 = "SELECT TOP 1 location FROM RFID ORDER BY time DESC"; 

    $query = sqlsrv_query($conn, $sql1);

    if ($query === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
    return $row ? $row['location'] : 'Unknown';
}

$latestRfidValue = getLatestRfidValue(); // Fetch the latest RFID value

function getAvailableSeats() {
    global $conn;

    $sql = "SELECT 
                COUNT(CASE WHEN CURRENT_LOCATION LIKE '%CBA%' THEN 1 END) AS cbaCount,
                COUNT(CASE WHEN CURRENT_LOCATION LIKE '%POLCA%' THEN 1 END) AS polcaCount,
                COUNT(CASE WHEN CURRENT_LOCATION LIKE '%Gate1%' THEN 1 END) AS gate1Count,
                COUNT(CASE WHEN CURRENT_LOCATION LIKE '%ULS%' THEN 1 END) AS ulsCount
            FROM USERPAGE";

    $query = sqlsrv_query($conn, $sql);

    if ($query === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
    $cbaCount = $row['cbaCount'];
    $polcaCount = $row['polcaCount'];
    $gate1Count = $row['gate1Count'];
    $ulsCount = $row['ulsCount'];

    $totalCount = $cbaCount + $polcaCount + $gate1Count + $ulsCount;
    $availableSeats = 13 - $totalCount;

    sqlsrv_free_stmt($query);

    return $availableSeats;
}

$availableSeats = getAvailableSeats(); // Call getAvailableSeats and store the result in $availableSeats

// Check if it's an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode([
        'availableSeats' => getAvailableSeats(),
        'latestRfidValue' => getLatestRfidValue()
    ]);
    exit();
}

// Close the database connection
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="userhomepage.css">
    <title>IKOT LASALLE</title>
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


.container {
    position: relative;
    background: transparent;
    background-position: center;
    background-size: 100%;
    background-repeat: no-repeat;
    border-radius: 30px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.8);
    padding: 20px;
    max-width: 400px;
    width: 1000px;
    margin: auto;
    z-index: 1;
    
}

.container::before{
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

.top-center,
.middle-center {
    text-align: center;
}

.usertitle h1 {
    color: white;
}

.Status {
    margin-top: 20px;
}

#userStatusText {
    color: blue;
}

#jeep {
    width: 30%;
    padding: 8px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.FillupForm {
    max-width: 400px;
    margin: 0 auto;
}

label {
    display: block;
    margin-bottom: 8px;
    color:  White;
    font-weight: bold;
}

input[type="text"],
select {
    width: 40%;
    padding: 8px;
    margin-bottom: 16px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    background-color: rgb(224, 251, 242);
    border-radius: 4px;
}

input[type="submit"] {
    background-color: rgb(4, 88, 83);
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    
}
    h2{
        color: white;
        font-family: 'Courier New', Courier, monospace;
    }
   
        
    

    </style>
</head>
<body>
    <div class="container">
    <div>
    <h2>Welcome, <?php echo htmlspecialchars($loggedInUserName); ?></h2>
    <h2>Your Balance: <?php echo htmlspecialchars($userBalance); ?></h2>
</div>

        <section id="title" class="top-center"> 
            <div class="usertitle">     
                <h1 >PASSENGER</h1>   
            </div>
            <div class="Status">
    <h3 id="userStatusText"></h3>
    <label for="jeep">JEEP LOCATION:</label>
    <input type="text" id="jeep" name="jeep" value="<?php echo htmlspecialchars($latestRfidValue); ?>"><br><br>
</div>
        </section>
        <form id="passengerfillup" action="Userpagesubmit.php" method="POST" class="middle-center">  
            <div class="FillupForm">
                <label for="location"> Your Location:</label>
                <input type="text" id="location" name="location"><br><br>
                <label for="destination">Your Destination:</label>
                <select name="destination" id="destination">
                    <option value="Gate1">Gate1</option>
                    <option value="POLCA">POLCA</option>
                    <option value="CBA">CBA</option>
                    <option value="ULS">ULS</option>
                </select><br><br>
               
                <label for="seats">Seats Available:</label>
                <input type="text" id="seats" name="seats" value="<?php echo $availableSeats; ?>" readonly><br><br>

                <input type="submit">
                
            </div>
        </form>
        <form id="passengerfillup" action="Userpage.php" method="POST" class="middle-center">
            <input type="hidden" name="totalCount" value="<?php echo $totalCount; ?>">
        </form>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>

// Function to update available seats
function updateAvailableSeats() {
    $.ajax({
                url: 'Userpage.php',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $('#seats').val(data.availableSeats);
                    $('#jeep').val(data.latestRfidValue); // Update the RFID value
                },
                error: function (error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

// Call the update function initially
updateAvailableSeats();

// Set up a timer to call the update function every 3 seconds
setInterval(updateAvailableSeats, 3000);
// Attach an event listener to the form submission
document.getElementById("passengerfillup").addEventListener("submit", function (event) {
    // Prevent the form from submitting
    event.preventDefault();

    // Check if available seats are 0
    if ($('#seats').val() <= 0) {
        alert("Already Full. Please wait for the drop-off.");
    } else {
        // If available seats are not 0, submit the form
        this.submit();
    }
});
</script> 
<script>
    window.onload = function () {
        var message = '<?php echo $message; ?>';
        if (message !== "") {
            alert(message);
            // Disable the form submission if the message is not empty
            document.getElementById("passengerfillup").onsubmit = function () {
                alert("Already Full. Sorry.");
                return false;
            };
        }   
         // Calculate available seats and update the input field
         var availableSeats = 13 - totalCount;
        document.getElementById("seats").value = availableSeats;
    };
</script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<script>
$(document).ready(function() {
    // Function to update user status
    function updateUserStatus() {
        // Fetch the latest status using AJAX
        $.ajax({
            url: 'getStatus.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Update the userStatusText element with the latest status
                $('#userStatusText').text(data.status);
            },
            error: function() {
                console.log('Error fetching status');
            }
        });
    }

    // Call the update function initially
    updateUserStatus();

    // Set up a timer to call the update function every 3 seconds
    setInterval(updateUserStatus, 3000);
});
</script>

