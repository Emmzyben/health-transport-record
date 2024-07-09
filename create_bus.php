<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brightway";

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $busName = $_POST['busName'];
    $plateNumber = $_POST['plateNumber'];

    $stmt = $conn->prepare("SELECT id FROM bus_record WHERE plate_number = ?");
    $stmt->bind_param("s", $plateNumber);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Bus with plate number '$plateNumber' already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bus_record (bus_name, plate_number) VALUES (?, ?)");
        $stmt->bind_param("ss", $busName, $plateNumber);

        if ($stmt->execute()) {
            $message = "Bus record added successfully.";
        } else {
            $message = "Error adding bus record: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <title>Admin page</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <header id="header" style="position:sticky;top:0">
        <div class="div1">
          <img src="images/logo.png">
        </div>
        <div class="div2">
        <ul>
        <li style="color:white"><?php echo "Welcome, " . $_SESSION['username'] . "!"?></li>
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
        <a href="admin.php">Company records</a>
        <a href="transport.php">Transport records</a>
        <a href="generate.html">Generate report</a>
        <a href="insert.php">Insert transport record</a>
         
        <a href="patient.php">Create patient record</a>
        <a href="create_driver.php">Create Driver Record</a>
        <a href="create_bus.php">Create Bus Record</a>
        
            <a href="logout.php">Log Out</a></li>
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
<div id="divideAdmin">
<div class="divideAdmin2">
    <h3>Admin dashboard</h3>
    <ul>
        <li><a href="admin.php">Company records</a></li>
        <li><a href="transport.php">Transport records</a></li>
        <li><a href="generate.html">Generate report</a></li>
        <li><a href="insert.php">Insert transport record</a></li>
         
        <li><a href="patient.php">Create patient record</a></li>
        <li><a href="create_driver.php">Create Driver Record</a></li>
        <li><a href="create_bus.php">Create Bus Record</a></li>
        <li>
            <a href="logout.php">Log Out</a></li>
    </ul>
</div> 
   <div class="divideAdmin1">
    <div id="list" style="background-color: #fff;">
        <h2>Create Bus Record</h2> 
 <p>Enter Bus details</p>
 
 


 <form action="" method="post">
     <input type="text" name="busName" placeholder="Enter Bus Name" required><br>
     <input type="text" name="plateNumber" placeholder="Enter Plate Number" required><br><br>
     <input type="submit" id="submit" value="Submit">
     <?php
     if (!empty($message)) {
         echo '<p>' . $message . '</p>';
     }
     ?>
 </form>
     </div>
</div>  


</div>

</main>

<footer>
   <p>© Business All Rights Reserved.</p> 
</footer>


</body>
</html>