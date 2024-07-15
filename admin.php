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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function fetchAdminData($conn) {
    $sql = "SELECT id, name, username, phone FROM admin WHERE role = 'driver'";
    $result = $conn->query($sql);
    $adminData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $adminData[] = $row;
        }
    }

    return $adminData;
}



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

if (isset($_POST['deleteAdmin'])) {
    $adminId = $_POST['adminId'];

    echo "<script>
            if (confirm('Are you sure you want to delete this Driver record?')) {
                window.location.href = 'admin.php?confirmDeleteAdmin=true&adminId=$adminId';
            }
          </script>";
}

if (isset($_POST['deleteBusRecord'])) {
    $busRecordId = $_POST['busRecordId'];

    echo "<script>
            if (confirm('Are you sure you want to delete this bus record?')) {
                window.location.href = 'admin.php?confirmDeleteBus=true&busRecordId=$busRecordId';
            }
          </script>";
}

if (isset($_GET['confirmDeleteAdmin'])) {
    $adminId = $_GET['adminId'];

    $sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminId);

    if ($stmt->execute()) {
        $message = "Driver record deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Error deleting admin record: " . $stmt->error;
        $messageType = "error";
    }

    $stmt->close();
}

// Handle confirmed deletion for bus record
if (isset($_GET['confirmDeleteBus'])) {
    $busRecordId = $_GET['busRecordId'];

    $sql = "DELETE FROM bus_record WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $busRecordId);

    if ($stmt->execute()) {
        $message = "Bus record deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Error deleting bus record: " . $stmt->error;
        $messageType = "error";
    }

    $stmt->close();
    
}

// Fetch data
$adminData = fetchAdminData($conn);
$busRecordData = fetchBusRecordData($conn);

$conn->close();
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'];
    unset($_SESSION['message']);
    unset($_SESSION['messageType']);
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo.png">
    <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
    <title>Admin page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Style for confirmation popup */
        .confirmation-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* semi-transparent background */
            z-index: 1000; /* ensure it's on top of everything */
            justify-content: center;
            align-items: center;
        }

        .confirmation-popup-inner {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .btn {
            padding: 10px 20px;
            margin: 0 10px;
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
        <div id="divideAdmin">
            <div class="divideAdmin2">
                
                <ul id="myList">
                <h3>Admin dashboard</h3>
                    <li><a href="transport.php">Transport records</a></li>
                    <li><a href="generate.php">Generate report</a></li>
                    <li><a href="patient.php">Patient Records</a></li>
                    <li><a href="admin.php">Company records</a></li>  
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>

            <div class="divideAdmin1">
            <div id="notificationBar" class="notification-bar <?php echo !empty($messageType) ? 'notification-'.$messageType : ''; ?>" <?php echo !empty($message) ? 'style="display: block;"' : ''; ?>>
        <span class="close-btn" onclick="closeNotification()">&times;</span>
        <span id="notificationMessage"><?php echo !empty($message) ? $message : ''; ?></span>
    </div>

                <div>
                  <div class="add">
                     <span><a><b>Driver List <span style="font-weight:100;color:green">(Double-click cells to edit)</span></b></a></span> 
                   <span><a class="linkBtn" href="create_driver.php">Create Driver Record</a></span> 
                  </div>
               
  <table id="adminTable">
        <tr>
            <th>Driver Name</th>
            <th>Driver Username</th>
            <th>Phone Number</th>
            <th>Action</th>
        </tr>
        <?php foreach ($adminData as $admin): ?>
            <tr data-id="<?php echo $admin['id']; ?>">
                <td ondblclick="editCell(this, 'name')"><?php echo htmlspecialchars($admin['name']); ?></td>
                <td ondblclick="editCell(this, 'username')"><?php echo htmlspecialchars($admin['username']); ?></td>
                <td ondblclick="editCell(this, 'phone')"><?php echo htmlspecialchars($admin['phone']); ?></td>
                <td>
                    <button onclick="showConfirmation('admin', <?php echo $admin['id']; ?>)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
  

    function editCell(cell, field) {
        const originalValue = cell.innerText;
        const input = document.createElement('input');
        input.type = 'text';
        input.value = originalValue;
        input.onblur = function() {
            cell.innerText = input.value;
            if (input.value !== originalValue) {
                const row = cell.parentElement;
                const id = row.getAttribute('data-id');
                updateAdminData(id, field, input.value);
            }
        };
        input.onkeydown = function(event) {
            if (event.key === 'Enter') {
                input.blur();
            }
        };
        cell.innerText = '';
        cell.appendChild(input);
        input.focus();
    }

    function updateAdminData(id, field, value) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_admin.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                displayNotification(response.message, response.status);
            }
        };
        xhr.send(`id=${id}&field=${field}&value=${encodeURIComponent(value)}`);
    }

    function displayNotification(message, type) {
        const notificationBar = document.getElementById('notificationBar');
        const notificationMessage = document.getElementById('notificationMessage');

        notificationMessage.innerText = message;
        notificationBar.className = `notification-bar notification-${type}`;
        notificationBar.style.display = 'block';

        setTimeout(() => {
            notificationBar.style.display = 'none';
        }, 5000);
    }

 
    </script>
                </div>

                <div>
                <div class="add">
                     <span><a><b>Bus List <span style="font-weight:100;color:green">(Double-click cells to edit)</span></b></a></span> 
                   <span><a class="linkBtn" href="create_bus.php">Create Bus Record</a></span> 
                  </div>
                  <table id="busTable">
        <tr>
            <th>Bus Name</th>
            <th>Plate Number</th>
            <th>Action</th>
        </tr>
        <?php foreach ($busRecordData as $busRecord): ?>
            <tr data-id="<?php echo $busRecord['id']; ?>">
                <td ondblclick="editCell(this, 'bus', 'bus_name')"><?php echo htmlspecialchars($busRecord['bus_name']); ?></td>
                <td ondblclick="editCell(this, 'bus', 'plate_number')"><?php echo htmlspecialchars($busRecord['plate_number']); ?></td>
                <td>
                    <button onclick="showConfirmation('bus', <?php echo $busRecord['id']; ?>)">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
 
    function editCell(cell, type, field) {
        const originalValue = cell.innerText;
        const input = document.createElement('input');
        input.type = 'text';
        input.value = originalValue;
        input.onblur = function() {
            cell.innerText = input.value;
            if (input.value !== originalValue) {
                const row = cell.parentElement;
                const id = row.getAttribute('data-id');
                updateRecordData(type, id, field, input.value);
            }
        };
        input.onkeydown = function(event) {
            if (event.key === 'Enter') {
                input.blur();
            }
        };
        cell.innerText = '';
        cell.appendChild(input);
        input.focus();
    }

    function updateRecordData(type, id, field, value) {
        const xhr = new XMLHttpRequest();
        const endpoint = type === 'admin' ? 'update_admin.php' : 'update_bus.php';
        xhr.open('POST', endpoint, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                displayNotification(response.message, response.status);
            }
        };
        xhr.send(`id=${id}&field=${field}&value=${encodeURIComponent(value)}`);
    }

    function displayNotification(message, type) {
        const notificationBar = document.getElementById('notificationBar');
        const notificationMessage = document.getElementById('notificationMessage');

        notificationMessage.innerText = message;
        notificationBar.className = `notification-bar notification-${type}`;
        notificationBar.style.display = 'block';

        setTimeout(() => {
            notificationBar.style.display = 'none';
        }, 5000);
    }

 
    </script>
                </div>
            </div>
        </div>

        <!-- Confirmation Popup -->
        <div id="confirmationPopup" class="confirmation-popup">
            <div class="confirmation-popup-inner">
                <p id="confirmationMessage"></p>
                <button class="btn" onclick="deleteRecord()">Yes</button>
                <button class="btn" onclick="closeConfirmation()">No</button>
            </div>
        </div>

        <script>
            function showConfirmation(type, id) {
                var message = type === 'admin' ? 'Are you sure you want to delete this admin record?' : 'Are you sure you want to delete this bus record?';
                document.getElementById('confirmationMessage').textContent = message;
                document.getElementById('confirmationPopup').style.display = 'flex';
                document.getElementById('confirmationPopup').setAttribute('data-type', type);
                document.getElementById('confirmationPopup').setAttribute('data-id', id);
            }

            function closeConfirmation() {
                document.getElementById('confirmationPopup').style.display = 'none';
            }

            function deleteRecord() {
                var type = document.getElementById('confirmationPopup').getAttribute('data-type');
                var id = document.getElementById('confirmationPopup').getAttribute('data-id');

                if (type === 'admin') {
                    window.location.href = 'admin.php?confirmDeleteAdmin=true&adminId=' + id;
                } else if (type === 'bus') {
                    window.location.href = 'admin.php?confirmDeleteBus=true&busRecordId=' + id;
                }
            }
        </script>
    </main>

    <footer>
        <p>© Business All Rights Reserved.</p>
    </footer>
    <script src="script.js"></script>
    <script>
        function closeNotification() {
            var notificationBar = document.getElementById("notificationBar");
            notificationBar.style.display = "none";
        }


    </script>
</body>
</html>
