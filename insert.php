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
$patients = [];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT first_name, last_name FROM patients ORDER BY first_name, last_name");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

$conn->close();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];
    $pickup_driver = $_POST['pickup_driver'];
    $recordType = $_POST['recordType'];
    $pickup_bus_name = $_POST['pickup_bus_name'];
    $pickup_bus_number = $_POST['pickup_bus_number'];
    
    $selected_patients = [];
    if (isset($_POST['selected_patients']) && is_array($_POST['selected_patients'])) {
        foreach ($_POST['selected_patients'] as $selected_patient) {
            $selected_patients[] = $selected_patient;
        }
    }
   
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("INSERT INTO detailed_records (patient_first_name, patient_last_name, record_date, record_time, driver, record_type, bus_name, bus_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $last_name, $pickup_date, $pickup_time, $pickup_driver, $recordType, $pickup_bus_name, $pickup_bus_number);
    
    foreach ($selected_patients as $patient) {
        list($first_name, $last_name) = explode(' ', $patient, 2);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    
  
    $message = "Transport records submitted successfully!";
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <title>insert record</title>
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
<div id="divide">
    <div class="divo">
        <div >
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
    </div>
    
    <div class="divide1">
        <h3>Patient List</h3>
        <p>Select Patient</p>
        <form id="patientForm">
            <table>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Select</th>
                </tr>
                <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['last_name']); ?></td>
                    <td><input type="checkbox" name="selected_patients[]" value="<?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>"></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </form>
    </div>
 

    <div class="divide2">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="copySelectedPatients()">
            <label for="pickup_date">Date</label><br>
            <input type="date" id="pickup_date" name="pickup_date" required><br>
            <label for="pickup_time">Time</label><br>
            <input type="time" id="pickup_time" name="pickup_time" required><br>
            <label for="pickup_driver">Driver</label><br>
            <input type="text" id="pickup_driver" name="pickup_driver" required><br>
            <label for="recordType">Transport record type</label><br>
            <select id="recordType" name="recordType" required>
                <option value="">Select record type</option>
                <option value="Pick-up">Pick Up</option>
                <option value="Drop-off">Drop Off</option>
            </select><br>
            <label for="pickup_bus_name">Bus name</label><br>
            <input type="text" id="pickup_bus_name" name="pickup_bus_name" required><br>
            <label for="pickup_bus_number">Bus number</label><br>
            <input type="text" id="pickup_bus_number" name="pickup_bus_number" required><br>
            <div id="selectedPatientsContainer"></div>
            <input type="submit" value="Submit" style='background-color:#007aff;color:white'>
        </form>
        
        <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>
</div>
<script>
function copySelectedPatients() {
    const checkboxes = document.querySelectorAll('#patientForm input[type="checkbox"]:checked');
    const container = document.getElementById('selectedPatientsContainer');
    container.innerHTML = ''; // Clear any existing inputs

    checkboxes.forEach((checkbox) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_patients[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });
}
</script>
</main>

<footer>
   <p>© Business All Rights Reserved.</p> 
</footer>


</body>
</html>
