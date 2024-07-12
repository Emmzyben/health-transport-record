<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'driver') {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brightway";

$detailed_records = [];
$driver_username = $_SESSION['username'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$sql = "SELECT record_date, record_time, COUNT(*) AS no_of_patients, record_type, bus_number FROM detailed_records WHERE driver = ? GROUP BY record_date, record_time, record_type, bus_number ORDER BY record_date DESC, record_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $driver_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $detailed_records[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
    <title>Driver Page</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <header id="header" style="position:sticky;top:0">
        <div class="div1">
          <img src="images/logo.png">
        </div>
        <div class="div2">
        <ul>
        
            <li><a href="driverPage.php">Home</a></li>
           <li><a href="driverinsert.php">Insert Record</a></li>
            <li> <a href="logout.php">Log Out</a></li>
            
    </ul>
    
        </div>
      </header>
     <aside> 
    <div>
      <img src="images/logo.png" alt="" >
  </div> 
      <div onclick="openNav()" >
          <div class="container" onclick="myFunction(this)" id="sideNav" >
              <p style="border: 1px solid white;padding: 10px;border-radius: 7px;color: white;">Menu</p>
            </div>
          </div>
  </aside>

  <nav style="z-index: 1;">
    <div id="mySidenav" class="sidenav">
        <img src="images/logo.png" alt="">
        <a href="driverPage.php">Home</a>
        <a href="driverinsert.php">Insert record</a> 

            <a href="logout.php">Log Out</a>
    </div>
    <script>
    
function myFunction(x) {
    x.classList.toggle("change");
  }

  var open = false;

function openNav() {
    var sideNav = document.getElementById("mySidenav");
    
    if (sideNav.style.width === "0px" || sideNav.style.width === "") {
        sideNav.style.width = "250px";
        open = true;
    } else {
        sideNav.style.width = "0";
        open = false;
    }
}
    </script>
</nav>
<main>
<div style="padding: 20px;height: 700px;">
<div>
<h3><?php echo "Welcome, " . $_SESSION['username'] . "!"?></h3>
    <h4>Transport Records</h4>
    <p>Record for pick-up and drop-off</p>
    <div style="overflow: auto;">
        <table id="driverTable">
            <tr>
                <th>Record type</th>
                <th>Date</th>
                <th>Time</th>
                <th>Bus number</th>
                <th>Total Passengers</th>
                
            </tr>
            <?php foreach ($detailed_records as $record) : ?>
                <tr>
                    <td><?php echo $record['record_type']; ?></td>
                    <td><?php echo $record['record_date']; ?></td>
                    <td><?php echo $record['record_time']; ?></td>
                     <td><?php echo $record['bus_number']; ?></td>
                    <td><?php echo $record['no_of_patients']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</div>

</main>

<footer>
   <p>© Business All Rights Reserved.</p> 
</footer>


 <script src="script.js"></script>
</body>
</html>