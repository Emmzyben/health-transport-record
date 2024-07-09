<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brightway";

$message = '';

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch admin data
function fetchAdminData($conn) {
    $sql = "SELECT id, name, phone FROM admin WHERE role = 'driver'";
    $result = $conn->query($sql);
    $adminData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $adminData[] = $row;
        }
    }

    return $adminData;
}


// Function to fetch bus record data
function fetchBusRecordData($conn) {
    $sql = "SELECT id, bus_name, plate_number FROM bus_record";
    $result = $conn->query($sql);
    $busRecordData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $busRecordData[] = $row;
        }
    }

    return $busRecordData;
}

// Delete functionality for admin
if (isset($_POST['deleteAdmin'])) {
    $adminId = $_POST['adminId'];

    $sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminId);

    if ($stmt->execute()) {
        $message = "Admin record deleted successfully.";
    } else {
        $message = "Error deleting admin record: " . $stmt->error;
    }

    $stmt->close();
}

// Delete functionality for bus record
if (isset($_POST['deleteBusRecord'])) {
    $busRecordId = $_POST['busRecordId'];

    $sql = "DELETE FROM bus_record WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $busRecordId);

    if ($stmt->execute()) {
        $message = "Bus record deleted successfully.";
    } else {
        $message = "Error deleting bus record: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch data
$adminData = fetchAdminData($conn);
$busRecordData = fetchBusRecordData($conn);

$conn->close();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <title>Admin page</title>
<link rel="stylesheet" href="style.css">
<style>
    form{
        margin:0px;
        padding:0;
        height:10px;
        margin-top:-18px
    }
  
   
</style>
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
    <div>
        <h3>Driver List</h3>
        <table>
            <tr>
                <th>Driver Name</th>
                <th>Phone Number</th>
                <th>Action</th>
            </tr>
            <?php foreach ($adminData as $admin): ?>
            <tr>
                <td><?php echo htmlspecialchars($admin['name']); ?></td>
                <td><?php echo htmlspecialchars($admin['phone']); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="adminId" value="<?php echo htmlspecialchars($admin['id']); ?>">
                        <input type="submit" name="deleteAdmin" value="Delete" id="submit">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div>
        <h3>Bus List</h3>
        <table>
            <tr>
                <th>Bus Name</th>
                <th>Plate Number</th>
                <th>Action</th>
            </tr>
            <?php foreach ($busRecordData as $busRecord): ?>
            <tr>
                <td><?php echo htmlspecialchars($busRecord['bus_name']); ?></td>
                <td><?php echo htmlspecialchars($busRecord['plate_number']); ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="busRecordId" value="<?php echo htmlspecialchars($busRecord['id']); ?>">
                        <input type="submit" name="deleteBusRecord" value="Delete" id="submit">
                    </form>
                </td>
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


</body>
</html>