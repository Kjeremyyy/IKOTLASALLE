<?php
// Start the session
session_start();

// Check if the user is not logged in, redirect to index.html
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.html");
    exit;
}

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

// Query to fetch data from PASSENGERREG table
$sql = "SELECT * FROM PASSENGERREG";
$result = mysqli_query($conn, $sql);


$sql2 = "SELECT * FROM DRIVERREG";
$result2 = mysqli_query($conn, $sql2);

$sql3 = "SELECT * FROM Transactions";
$result3 = mysqli_query($conn, $sql3);

$sql4 = "SELECT * FROM DRIVERTRANS"; 
$result4 = mysqli_query($conn, $sql4);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .side-panel {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 20%;
            padding: 10px;
            background-color: #f0f0f0;
        }
        .btn {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 5px;
    text-align: center;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 8px; /* Added border-radius */
}

.btn:hover {
    background-color: #45a049;
}

.button {
    display: flex;
    gap: 10px;
}

button.adminlogin {
    background-color: #3498db;
}

/* Input Styles */
input {
    width: 100%; /* Modified width to fill the container */
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
        .main-content {
            margin-left: 20%; /* Adjust to match the width of the side panel */
            margin-top: 20px; /* Adjust the top margin to provide space between side panel and table */
        }
        .table-container {
            margin: 0 auto; /* Center the table */
            width: 80%; /* Adjust the width as needed */
        }
        .search-bar {
            display: none;
        }
    </style>
</head>
<body>

<div class="side-panel">
 <center>   <h1>ADMIN</h1></center>
    <button class="btn" onclick="showPassengerData()">Passenger</button> 
    <div id="passenger-search" class="search-bar">
        <input type="text" id="passenger-filter" onkeyup="filterPassenger()" placeholder="Search by USERID...">  
    </div>
    <br>
    <!-- Add Balance Section -->
    <div id="add-balance-section" style="display: none;">
        <h3>Add Balance</h3>
        <form id="add-balance-form" action="add_balance.php" method="post">
            <input type="text" id="user-id-input" name="user_id" placeholder="Enter User ID">
            <input type="text" id="add-balance-input" name="add_balance" placeholder="Enter Amount to Add">
            <button type="submit">Submit</button>
        </form>
    </div>
    <br>
    <button class="btn" onclick="showDriverData()">Driver</button> 
    <div id="driver-search" class="search-bar">
        <input type="text" id="driver-filter" onkeyup="filterDriver()" placeholder="Search by LICENSED_NUMBER..."> 
    </div>
    <br>
    <button class="btn" onclick="showTransactions()">Passenger Transactions</button><br>


    <!-- Driver Transaction Section -->
    <button class="btn" onclick="showDriverTransactions()">Driver Transactions</button>
    <div id="driver-transaction-section" style="display: none;">
        <h3>Driver Transaction</h3>
        <form id="withdraw-form" action="withdraw_amount.php" method="post">
    <input type="text" name="licensed_number" placeholder="Enter Licensed Number">
    <input type="text" name="withdrawal_amount" placeholder="Enter Withdrawal Amount">
    <button type="submit">Submit</button>
</form>
    </div>
    
</div>




<div class="main-content">
    <center><h2 id="passenger-heading">Passenger Registration Data</h2></center>
    <div class="table-container">
        <table id="passenger-table">
            <tr>
                <th>User ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Password</th>
                <th>Balance</th>
            </tr>
            <?php
            // Output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['USERID'] . "</td>";
                echo "<td>" . $row['NAME'] . "</td>";
                echo "<td>" . $row['USERNAME'] . "</td>";
                echo "<td>" . $row['PASSWORD'] . "</td>";
                echo "<td>" . $row['BALANCED'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <center><h2 id="driver-heading">Driver Registration Data</h2></center>
    <div class="table-container">
        <table id="driver-table">
            <tr>
                <th>Licensed Number</th>
                <th>Name</th>
                <th>Password</th>
                <th>Wallet</th>
            </tr>
            <?php
            // Output data of each row
            while ($row = mysqli_fetch_assoc($result2)) {
                echo "<tr>";
                echo "<td>" . $row['LICENSED_NUMBER'] . "</td>";
                echo "<td>" . $row['NAME'] . "</td>";
                echo "<td>" . $row['PASSWORD'] . "</td>";
                  echo "<td>" . $row['Wallet'] . "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <center><h2 id="transaction-heading" style="display: none;">Passenger Transactions</h2></center>
<div class="table-container" id="transaction-table-container" style="display: none;">
    <table id="transaction-table">
        <tr>
            <th>Transaction Number</th>
            <th>User ID</th>
            <th>Location</th>
            <th>Destination</th>
            <th>Fare</th>
            <th>Time</th>
        </tr>
        <?php
        // Output data of each row
        while ($row = mysqli_fetch_assoc($result3)) {
            echo "<tr>";
            echo "<td>" . $row['Transaction_number'] . "</td>";
            echo "<td>" . $row['USERID'] . "</td>";
            echo "<td>" . $row['Location'] . "</td>";
            echo "<td>" . $row['Destination'] . "</td>";
            echo "<td>" . $row['Fare'] . "</td>";
            echo "<td>" . $row['time'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>
<center><h2 id="driver-transaction-heading" style="display: none;">Driver Transactions</h2></center>
<div class="table-container" id="driver-transaction-table-container" style="display: none;">
    <table id="driver-transaction-table">
        <tr>
            <th>Transaction Number</th>
            <th>Licensed Number</th>
            <th>Withdrawal</th>
            <th>Time</th>
        </tr>
        <?php
        // Output data of each row
       while ($row = mysqli_fetch_assoc($result4)) {
    echo "<tr>";
    echo "<td>" . $row['Transaction_number'] . "</td>";
    echo "<td>" . $row['LICENSED_NUMBER'] . "</td>";
    echo "<td>" . $row['Withdrawal'] . "</td>";
    echo "<td>" . $row['Time'] . "</td>";
    echo "</tr>";
}
        ?>
    </table>
</div>
</div>



<script>
  function showPassengerData() {
    // Show passenger table and hide other elements
    document.getElementById('passenger-table').style.display = 'table';
    document.getElementById('driver-table').style.display = 'none';
    document.getElementById('transaction-table-container').style.display = 'none';
    document.getElementById('passenger-heading').style.display = 'block';
    document.getElementById('driver-heading').style.display = 'none';
    document.getElementById('transaction-heading').style.display = 'none';
    document.getElementById('passenger-search').style.display = 'block';
    document.getElementById('driver-search').style.display = 'none';
    document.getElementById('driver-transaction-section').style.display = 'none';
    document.getElementById('add-balance-section').style.display = 'block';
    document.getElementById('driver-transaction-table-container').style.display = 'none';
     document.getElementById('driver-transaction-heading').style.display = 'none';
     
}

function showDriverData() {
    // Show driver table and hide other elements
    document.getElementById('passenger-table').style.display = 'none';
    document.getElementById('driver-table').style.display = 'table';
    document.getElementById('transaction-table-container').style.display = 'none';
    document.getElementById('passenger-heading').style.display = 'none';
    document.getElementById('driver-heading').style.display = 'block';
    document.getElementById('transaction-heading').style.display = 'none';
    document.getElementById('passenger-search').style.display = 'none';
    document.getElementById('driver-search').style.display = 'block';
    document.getElementById('driver-transaction-section').style.display = 'none';
    document.getElementById('add-balance-section').style.display = 'none';
    document.getElementById('driver-transaction-table-container').style.display = 'none';
     document.getElementById('driver-transaction-heading').style.display = 'none';
}

function showTransactions() {
    // Show transaction table and hide other elements
    document.getElementById('passenger-table').style.display = 'none';
    document.getElementById('driver-table').style.display = 'none';
    document.getElementById('transaction-table-container').style.display = 'block';
    document.getElementById('passenger-heading').style.display = 'none';
    document.getElementById('driver-heading').style.display = 'none';
     document.getElementById('transaction-heading').style.display = 'block';
    document.getElementById('passenger-search').style.display = 'none';
    document.getElementById('driver-search').style.display = 'none';
    document.getElementById('driver-transaction-section').style.display = 'none';
    document.getElementById('add-balance-section').style.display = 'none';
     document.getElementById('driver-transaction-table-container').style.display = 'none';
     document.getElementById('driver-transaction-heading').style.display = 'none';
}

function showDriverTransactions() {
    // Show driver transaction section and hide other elements
    document.getElementById('driver-transaction-section').style.display = 'block';
   document.getElementById('passenger-table').style.display = 'none';
    document.getElementById('passenger-search').style.display = 'none';
    document.getElementById('transaction-table-container').style.display = 'none';
    document.getElementById('passenger-heading').style.display = 'none';
    document.getElementById('driver-search').style.display = 'none';
      document.getElementById('driver-table').style.display = 'none';
    document.getElementById('driver-heading').style.display = 'block';
   document.getElementById ('driver-transaction-table-container').style.display = 'table';
   document.getElementById('driver-heading').style.display = 'none';
   document.getElementById('driver-transaction-heading').style.display = 'block';
    document.getElementById('transaction-heading').style.display = 'none';
}




    function filterPassenger() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("passenger-filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("passenger-table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0]; // Filter based on the first column (USERID)
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function filterDriver() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("driver-filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("driver-table");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0]; // Filter based on the first column (LICENSED_NUMBER)
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    // Initially hide the driver table and its heading
    document.getElementById('driver-table').style.display = 'none';
    document.getElementById('driver-heading').style.display = 'none';
    document.getElementById('driver-search').style.display = 'none';
</script>

</body>
</html>

<?php
// Close connection
mysqli_close($conn);
?>