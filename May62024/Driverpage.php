<?php
header("Access-Control-Allow-Origin: *");
// Include the new file
include_once('updateStatus.php');

session_start(); // Start the session at the top

// Redirect user to login page if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.html");
    exit;
}

$serverName = "localhost";
$databaseName = "id22120907_ikottracker";
$username = "id22120907_ikotlasalletracker";
$password = "Ikottracker123!";

$conn = new mysqli($serverName, $username, $password, $databaseName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function fetchLatestLocation($conn) {
    $sql = "SELECT location FROM rfid ORDER BY time DESC LIMIT 1"; // Update this query if 'rfid' table doesn't exist
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return isset($row['location']) ? $row['location'] : '';
    } else {
        return '';
    }
}



$latestLocation = fetchLatestLocation($conn);

//--------------------------//

$sql = "SELECT 
            SUM(CASE WHEN CURRENT_LOCATION LIKE '%Gate1%' THEN 1 ELSE 0 END) AS gate1Count,
            SUM(CASE WHEN CURRENT_LOCATION LIKE '%POLCA%' THEN 1 ELSE 0 END) AS polcaCount,
            SUM(CASE WHEN CURRENT_LOCATION LIKE '%CBA%' THEN 1 ELSE 0 END) AS cbaCount,
            SUM(CASE WHEN CURRENT_LOCATION LIKE '%ULS%' THEN 1 ELSE 0 END) AS ulsCount
        FROM USERPAGE";

$query = $conn->query($sql);

if (!$query) {
    die("Query failed: " . $conn->error);
}

$row = $query->fetch_assoc();

// Convert null values to 0
$gate1Count = $row['gate1Count'] ?? 0;
$polcaCount = $row['polcaCount'] ?? 0;
$cbaCount = $row['cbaCount'] ?? 0;
$ulsCount = $row['ulsCount'] ?? 0;

// Check if it's an AJAX request and the requested action is 'fetchLocation'
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && isset($_GET['action']) && $_GET['action'] === 'fetchLocation') {
    // Fetch the jeep location
    $latestLocation = fetchLatestLocation($conn);

    // Return the location as JSON
    header('Content-Type: application/json');
    echo json_encode(['location' => $latestLocation]);
    exit;
}

// Check if it's an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // It's an AJAX request
    header('Content-Type: application/json');
    echo json_encode([
        'gate1Count' => $gate1Count,
        'polcaCount' => $polcaCount,
        'cbaCount' => $cbaCount,
        'ulsCount' => $ulsCount,
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $updateClockSql = "UPDATE DRIVER 
    SET TIME_OUT = NOW() 
    WHERE TIME_IN = (SELECT MAX(TIME_IN) FROM DRIVER)";
    $updateClockQuery = $conn->query($updateClockSql);

    if (!$updateClockQuery) {
        die("Query failed: " . $conn->error);
    }

    // Redirect to the login page or any other desired page
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" href="adminpage.css">
    <title>IKOT LASALLE</title>
    <style>
   body {
            /*background: linear-gradient(to right, #8e44ad, #3498db);*/
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
            flex-direction: column; /* Updated to column direction for centering */
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
        justify-content: center;
        background: transparent;
        background-position: center;
        background-size: 100%;
        background-repeat: no-repeat;
        border-radius: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.8); /* White shadow color */
        padding: 20px;
        max-width: none;
        width: auto;
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
    color: #333;
}

.Status {
    margin-top: 20px;
}

#statusButton {
    background-color: #3498db;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}



#statusText {
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

#passengerfillup input[type="text"] {
        display: block;
        margin: 0 auto;
        text-align: center;
        padding: 8px; /* Adjust the padding as needed */
        border: 1px solid #ccc;
        background-color: rgb(224, 251, 242);
        border-radius: 4px;
    }
    #passengerfillup button.submit {
    display: block;
    margin: 0 auto;
}

    #passengerfillup button.submit {
        display: block;
        margin: 0 auto;
        background-color: rgb(4, 88, 83);
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }


label {
    display: block;
    margin-bottom: 8px;
    color: #000000;
    font-weight: bold;
    text-align: center; 
}

input[type="text"],
select {
   
    padding: 8px;
    margin-bottom: 16px;
    box-sizing: border-box;
    border: 1px solid #ccc;
    background-color: rgb(224, 251, 242);
    border-radius: 4px;
    text-align: center;
    margin: 0 auto; /* Center the input within its container */
}


.button-style {
    display: block;
    margin: 0 auto;
    background-color: rgb(4, 88, 83);
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.container2 {
            display: flex;
            justify-content: space-evenly;
            width: 400px; /* Adjust the width as needed */
        }

@media only screen and (max-width: 768px) {
    .container {
        /* Adjust container styles for smaller screens */
        max-width: 75%;
    }

    /* Adjust other styles as needed for smaller screens */
    .usertitle h1 {
        font-size: 24px; /* Adjust font size for smaller screens */
    }

    /* Adjust layout for Waiting Passengers section */
    #passengerfillup {
        flex-direction: column; /* Display form elements vertically */
    }

    /* Adjust styles for form inputs */
    #passengerfillup input[type="text"] {
        width: 80%; /* Adjust width for smaller screens */
    }

    /* Adjust layout for button container */
    .container2 {
        flex-direction: column; /* Display buttons vertically */
        width: 100%; /* Occupy full width */
    }

    /* Adjust styles for buttons */
    .button-style {
        width: 50%; /* Adjust width for smaller screens */
        margin-bottom: 10px;
    }
}
    </style>
</head>
<body>
<div class="container">
        <section id="title" class="top-center">
            <div class="usertitle">     
                <h1>DRIVER</h1>   
            </div>
            <div class="Status" >
                <button type="button" id="statusButton" onclick="changeStatus()">Change Status</button>
                <h3 id="statusText"></h3>
            </div>
            
            <div class="Status">
    <label for="jeep"> JEEP LOCATION:</label>
    <input type="text" id="jeep" name="jeep" value="<?php echo htmlspecialchars($latestLocation); ?>" readonly><br><br>
</div>
        </section>
        <h3 align="center">Waiting Passengers</h3>
        <form id="passengerfillup" action="Driverpage.php" method="POST" style="display: flex; flex-direction: column; align-items: center;">
    <div style="display: flex; flex-direction: row; justify-content: space-between; width: 100%;">
        <div style="text-align: center; width: 48%;">
            <label for="gate1Count">Gate 1:</label>
            <input type="text" id="gate1Count" name="gate1Count" value="<?php echo $gate1Count; ?>" readonly><br><br>

            <label for="polcaCount">POLCA :</label>
            <input type="text" id="polcaCount" name="polcaCount" value="<?php echo $polcaCount; ?>" readonly><br><br>
        </div>

        <div style="text-align: center; width: 48%;">
            <label for="cbaCount">CBA:</label>
            <input type="text" id="cbaCount" name="cbaCount" value="<?php echo $cbaCount; ?>" readonly><br><br>

            <label for="ulsCount">ULS :</label>
            <input type="text" id="ulsCount" name="ulsCount" value="<?php echo $ulsCount; ?>" readonly><br><br>
        </div>
    </div>

    <button class="submit" name="submit" id="submit" type="submit">LOGOUT</button><br><br>
    
</form>
<div class="container2">
<form action="deleteDestination.php" method="post">
    <input type="hidden" name="jeepLocation" id="jeepLocation" value="<?php echo htmlspecialchars($latestLocation); ?>">
    <button type="submit" name="dropOffSubmit" id="deleteButton" class="button-style">DROP OFF</button>
</form>

<form id="pickupForm" action="pickup.php" method="post">
    <input type="hidden" name="jeepLocation" id="pickupJeepLocation" value="<?php echo htmlspecialchars($latestLocation); ?>">
    <button type="button" id="pickupButton" class="button-style">PICK UP</button>
</form>
</div>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <script>

function setStatusInLocalStorage(status) {
    localStorage.setItem('status', status);
}

// Function to get the status from local storage
function getStatusFromLocalStorage() {
    return localStorage.getItem('status') || 'NOT OPERATING';
}

function fetchJeepLocation() {
    $.ajax({
        url: 'Driverpage.php',
        type: 'GET',
        data: { action: 'fetchLocation' },
        dataType: 'json',
        success: function(data) {
            if (data && data.location !== undefined) {
                $('#jeep').val(data.location);
                $('#jeepLocation').val(data.location);
            } else {
                console.error('Invalid data received:', data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching jeep location:', status, error);
        }
    });
}

// Set up a timer to call the fetchJeepLocation function every 3 seconds
setInterval(fetchJeepLocation, 1000);

function pickup() {
    var jeepLocation = $('#jeep').val();

    $.ajax({
        url: 'pickup.php',
        type: 'POST',
        data: { jeepLocation: jeepLocation },
        dataType: 'json',
        success: function (data) {
            console.log('Pickup successful:', data);
            // You can handle the response here if needed
        },
        error: function (error) {
            console.error('Error picking up:', error);
        }
    });
}

// Bind the pickup function to the click event of the "Pick up" button
$('#pickupButton').on('click', pickup);


// Function to update available seats
function updateAvailableSeats() {
    $.ajax({
        url: 'Driverpage.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            console.log('Success:', data);
            // Update the counts
            $('#gate1Count').val(data.gate1Count);
            $('#polcaCount').val(data.polcaCount);
            $('#cbaCount').val(data.cbaCount);
            $('#ulsCount').val(data.ulsCount);
        },
        error: function(xhr, status, error) {
            console.error('Error updating available seats:', status, error);
        }
    });    
}

// Call the update function initially
updateAvailableSeats();

// Set up a timer to call the update function every 1 second
setInterval(updateAvailableSeats, 1000);

// Function to change the status
function changeStatus() {
    var statusTextElement = document.getElementById('statusText');
    var currentStatus = statusTextElement.textContent;

    if (currentStatus === 'NOT OPERATING') {
        statusTextElement.textContent = 'OPERATING';
    } else {
        statusTextElement.textContent = 'NOT OPERATING';
    }

    // Update the status in local storage
    setStatusInLocalStorage(statusTextElement.textContent);

    // Send an AJAX request to update the status
    $.ajax({
        url: 'updateStatus.php', // Updated URL to the new PHP file
        type: 'POST',
        data: { newStatus: statusTextElement.textContent },
        dataType: 'json',
        success: function (data) {
            console.log('Status updated successfully:', data);
        },
        error: function (error) {
            console.error('Error updating status:', error);
        }
    });
}

// Set the initial status from local storage
document.getElementById('statusText').textContent = getStatusFromLocalStorage();

</script>

</body>
</html>
