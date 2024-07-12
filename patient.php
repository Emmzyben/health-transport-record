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
$messageType = '';
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
            $_SESSION['message'] = "Patient already exists.";
           $_SESSION['messageType'] = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO patients (first_name, last_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $firstName, $lastName);

            if ($stmt->execute()) {
                $_SESSION['message']  = "Patient added successfully.";
                $_SESSION['messageType']  = 'success';
            } else {
                $_SESSION['message']= "Error adding patient: " . $stmt->error;
                $_SESSION['messageType']  = 'error';
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
            $_SESSION['message']= "Patient deleted successfully.";
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = "Error deleting patient: " . $stmt->error;
            $_SESSION['messageType'] = 'error';
        }

        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$result = $conn->query("SELECT id, first_name, last_name FROM patients ORDER BY first_name, last_name");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

$conn->close();
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link rel="shortcut icon" href="images/logo.png">
    <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
    <title>Day Activity and Health Services Daily Transportation Record</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .notification-bar {
            padding: 10px;
            text-align: center;
            z-index: 1050;
            display: none;
        }
        .notification-success {
            background-color: #d4edda;
            color: #155724;
        }
        .notification-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .close-btn {
            margin-left: 15px;
            color: #000;
            font-weight: bold;
            float: right;
            font-size: 20px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }
        .close-btn:hover {
            color: #999;
        }
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .popup-content {
            text-align: center;
        }
        .popup-btns {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #0056b3;
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
                <li style="color:white"><?php echo "Welcome, " . $_SESSION['username'] . "!" ?></li>
            </ul>
        </div>
    </header>
    <aside> 
        <div>
            <img src="images/logo.png" alt="">
        </div> 
        <div onclick="openNav()">
            <div class="container" onclick="myFunction(this)" id="sideNav">
                <p style="border: 1px solid white;padding: 10px;border-radius: 7px;color: white;">Menu</p>
            </div>
        </div>
    </aside>

    <nav style="z-index: 1;">
        <div id="mySidenav" class="sidenav">
            <a href="admin.php">Company records</a>
            <a href="transport.php">Transport records</a>
            <a href="generate.php">Generate report</a>
            <a href="insert.php">Insert transport record</a>
            <a href="patient.php">Create patient record</a>
            <a href="create_driver.php">Create Driver Record</a>
            <a href="create_bus.php">Create Bus Record</a>
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

            function showPopup(patientId) {
                var popup = document.getElementById('deletePopup');
                popup.style.display = 'block';
                document.getElementById('patientId').value = patientId;
            }

            function closePopup() {
                var popup = document.getElementById('deletePopup');
                popup.style.display = 'none';
            }
        </script>
    </nav>
    <main>
        <div id="divideAdmin">
            <div class="divideAdmin2">
                <ul id="myList">
                <h3>Admin dashboard</h3>
                    <li><a href="admin.php">Company records</a></li>
                    <li><a href="transport.php">Transport records</a></li>
                    <li><a href="generate.php">Generate report</a></li>
                    <li><a href="insert.php">Insert transport record</a></li>
                    <li><a href="patient.php">Create patient record</a></li>
                    <li><a href="create_driver.php">Create Driver Record</a></li>
                    <li><a href="create_bus.php">Create Bus Record</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div> 
            <div class="divideAdmin1">
                <?php
                    if (!empty($message)) {
                        echo '<div id="notificationBar" class="notification-bar notification-' . $messageType . '">';
                        echo $message;
                        echo '<span class="close-btn" onclick="closeNotification()">&times;</span>';
                        echo '</div>';
                    }
                ?>
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
                        </form>
                    </div> 
                </div>
                <div id="list">
                    <p>Patient Information</p>
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
                                                <button onclick="showPopup(<?php echo $patient['id']; ?>)">Delete</button>
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

    <!-- Delete Confirmation Popup -->
    <div id="deletePopup" class="popup">
        <div class="popup-content">
            <p>Are you sure you want to delete this patient?</p>
            <div class="popup-btns">
                <form id="deleteForm" action="" method="post">
                    <input type="hidden" name="patientId" id="patientId" value="">
                    <button type="submit" name="deletePatient" class="btn">Delete</button>
                    <button type="button" onclick="closePopup()" class="btn">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function closeNotification() {
            document.getElementById("notificationBar").style.display = "none";
        }

        // Show the notification bar if there's a message
        document.addEventListener("DOMContentLoaded", function() {
            var notificationBar = document.getElementById("notificationBar");
            if (notificationBar) {
                notificationBar.style.display = "block";
                setTimeout(closeNotification, 5000); // Auto-hide after 5 seconds
            }
        });
    </script>
 <script src="script.js"></script>
</body>
</html>
