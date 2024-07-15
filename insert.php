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
$messageType = '';

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
$drivers = [];
$result = $conn->query("SELECT id, name FROM admin WHERE role = 'driver'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }
}


$buses = [];
$result = $conn->query("SELECT id, bus_name, plate_number FROM bus_record");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $buses[] = $row; 
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
        $name_parts = explode(' ', $patient, 2);
        if (count($name_parts) == 2) {
            $first_name = $name_parts[0];
            $last_name = $name_parts[1];
            $stmt->execute();
        } else {
            error_log("Invalid patient name: " . $patient);
        }
    }

    $stmt->close();
    $conn->close();

    
  
    $_SESSION['message'] = "Transport records submitted successfully!";
    $_SESSION['messageType']= 'success';
    header("Location: transport.php");
    exit;
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
    <title>insert record</title>
<link rel="stylesheet" href="style.css">
<style>
      
        .divide2{
            width: 20%;
        }
        @media only screen and (max-width: 900px) {
  
      .divide2{
        width: auto;height: auto;position: unset;
      }}
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
            <a href="transport.php">Transport records</a>
            <a href="generate.php">Generate report</a>
            <a href="admin.php">Company records</a>  
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
<div id="divide">
    <div class="divo">
        <div >
        <ul id="myList">
                <h3>Admin dashboard</h3>
                    <li><a href="transport.php">Transport records</a></li>
                    <li><a href="generate.php">Generate report</a></li>
                    <li><a href="patient.php">Patient Records</a></li>
                    <li><a href="admin.php">Company records</a></li>  
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
    </div> 
    </div>
    
    <div class="divide1">
 
        <h3> New Transport Record</h3>
        <p>Select Patient</p>
        <form id="patientForm">
    <table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Select All <input type="checkbox" id="selectAll"></th>
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

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('#patientForm input[type="checkbox"]');
        for (var checkbox of checkboxes) {
            checkbox.checked = this.checked;
        }
    });
</script>

    </div>
 

    <div class="divide2">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="copySelectedPatients()">
            <label for="pickup_date">Date</label><br>
            <input type="date" id="pickup_date" name="pickup_date" required><br>
            <label for="pickup_time">Time</label><br>
            <input type="time" id="pickup_time" name="pickup_time" required><br>
            <label for="pickup_driver">Driver</label><br>
<select id="pickup_driver" name="pickup_driver" required>
    <option value="">Select Driver</option>
    <?php foreach ($drivers as $driver): ?>
        <option value="<?php echo htmlspecialchars($driver['name']); ?>"><?php echo htmlspecialchars($driver['name']); ?></option>
    <?php endforeach; ?>
</select>

            <label for="recordType">Transport record type</label><br>
            <select id="recordType" name="recordType" required>
                <option value="">Select record type</option>
                <option value="Pick up">Pick Up</option>
                <option value="Drop off">Drop Off</option>
            </select><br>
            <input type="text" id="pickup_bus_name" name="pickup_bus_name" hidden>

<label for="pickup_bus_number">Bus number</label><br>
<select id="pickup_bus_number" name="pickup_bus_number" required>
    <option value="">Select Bus Number</option>
    <?php foreach ($buses as $bus): ?>
        <option value="<?php echo htmlspecialchars($bus['plate_number']); ?>" 
                data-bus-name="<?php echo htmlspecialchars($bus['bus_name']); ?>">
            <?php echo htmlspecialchars($bus['plate_number']); ?>
        </option>
    <?php endforeach; ?>
</select><br>
<script>
    document.getElementById('pickup_bus_number').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var busName = selectedOption.getAttribute('data-bus-name');
        document.getElementById('pickup_bus_name').value = busName;
    });
</script>


            <div id="selectedPatientsContainer"></div>
            <input type="submit" value="Submit" style='background-color:#007aff;color:white'>
        </form>
        
       
    </div>
</div>
<script>
function copySelectedPatients() {
    const checkboxes = document.querySelectorAll('#patientForm input[type="checkbox"]:checked');
    const container = document.getElementById('selectedPatientsContainer');
    container.innerHTML = '';

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


 <script src="script.js"></script>
</body>
</html>
