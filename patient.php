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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addPatient'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];

        // Check if the patient already exists
        $stmt = $conn->prepare("SELECT id FROM patients WHERE first_name = ? AND last_name = ?");
        $stmt->bind_param("ss", $firstName, $lastName);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Patient already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO patients (first_name, last_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $firstName, $lastName);

            if ($stmt->execute()) {
                $message = "Patient added successfully.";
            } else {
                $message = "Error adding patient: " . $stmt->error;
            }
        }

        $stmt->close();
    }

    if (isset($_POST['deletePatient'])) {
        $patientId = $_POST['patientId'];

        // Delete the patient
        $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
        $stmt->bind_param("i", $patientId);

        if ($stmt->execute()) {
            $message = "Patient deleted successfully.";
        } else {
            $message = "Error deleting patient: " . $stmt->error;
        }

        $stmt->close();
    }
}

$result = $conn->query("SELECT id, first_name, last_name FROM patients ORDER BY first_name, last_name");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

$conn->close();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <title>Day Activity and Health Services
Daily Transportation Record</title>
<link rel="stylesheet" href="style.css">
<style>
    #form{
        margin:0;
        padding:0;
        height:0;
        margin-top:-30px
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
         
           <div id="enter">
       <div>
    <h1>Form for Entering Patient Names</h1>
    <p>Fill in the form with the required details</p>
</div>
<div>
 <form action="" method="post">
        <label for="firstName">First name</label><br>
        <input type="text" name="firstName" placeholder="Patient first name"><br>
        <label for="lastName">Last name</label><br>
        <input type="text" name="lastName" placeholder="Patient last name"><br><br>
        <input type="submit" name="addPatient" value="Add Patient" id="submit">
        <?php
    if (!empty($message)) {
        echo '<p>' . $message . '</p>';
    }
    ?>
    </form>
</div> 
    </div>


    <div id="list">
       <p> Patient Information</p>
       <h2>List of All Patients</h2> 
<div>
<?php if (!empty($patients)): ?>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['last_name']); ?></td>
                            <td>
                                <form method="post" id="form">
                                    <input type="hidden" name="patientId" value="<?php echo htmlspecialchars($patient['id']); ?>">
                                    <input type="submit" name="deletePatient" value="Delete" id="submit">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

</div>
    </div>
        </div>  
        
        
        </div>
    
</main>

<footer>
   <p>© Business All Rights Reserved.</p> 
</footer>


</body>
</html>