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

$detailed_records = [];
$record_type_filter = isset($_GET['record_type']) ? $_GET['record_type'] : '';
$driver_filter = isset($_GET['driver']) ? $_GET['driver'] : '';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteRecord'])) {
    $recordId = $_POST['recordId'];

    echo "<script>
            if (confirm('Are you sure you want to delete this record?')) {
                window.location.href = 'transport.php?confirmDelete=true&recordId=$recordId';
            }
          </script>";
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['confirmDelete'])) {
    $recordId = $_GET['recordId'];

    $stmt = $conn->prepare("DELETE FROM detailed_records WHERE id = ?");
    $stmt->bind_param("i", $recordId);

    if ($stmt->execute()) {
        $message = "Record deleted successfully.";
    } else {
        $message = "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

$sql = "SELECT * FROM detailed_records";
if ($record_type_filter || $driver_filter) {
    $sql .= " WHERE ";
    $filters = [];
    if ($record_type_filter) {
        $filters[] = "record_type = '" . $conn->real_escape_string($record_type_filter) . "'";
    }
    if ($driver_filter) {
        $filters[] = "driver = '" . $conn->real_escape_string($driver_filter) . "'";
    }
    $sql .= implode(" AND ", $filters);
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $detailed_records[] = $row;
    }
}

$drivers_sql = "SELECT DISTINCT driver FROM detailed_records";
$drivers_result = $conn->query($drivers_sql);
$drivers = [];
if ($drivers_result->num_rows > 0) {
    while ($row = $drivers_result->fetch_assoc()) {
        $drivers[] = $row['driver'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo.png">
    <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
    <title>Admin page</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .confirmation-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 1000;
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
</head>
<body>
    <header id="header" style="position: sticky; top: 0;">
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
                <p style="border: 1px solid white; padding: 10px; border-radius: 7px; color: white;">Menu</p>
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
        </script>
    </nav>
    <main>
        <div id="divideAdmin">
            <div class="divideAdmin2">
                <ul id="myList">
                <h3>Admin dashboard</h3>
                    <li><a href="admin.php">Company records</a></li>
                    <li><a href="transport.php">Transport records</a></li>
                    <li><a href="generate.php">Generate transport record</a></li>
                    <li><a href="insert.php">Insert transport record</a></li>
                    <li><a href="patient.php">Create patient record</a></li>
                    <li><a href="create_driver.php">Create Driver Record</a></li>
                    <li><a href="create_bus.php">Create Bus Record</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="divideAdmin1">
                <h3>Transportation records</h3>
                <form method="get" action="">
                    <label for="record_type">Sort by Record Type:</label>
                    <select name="record_type" id="record_type" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="Pick up" <?php if ($record_type_filter == 'Pick up') echo 'selected'; ?>>Pick Up</option>
                        <option value="Drop off" <?php if ($record_type_filter == 'Drop off') echo 'selected'; ?>>Drop Off</option>
                    </select>
                    <span style="margin-left:10px"></span>
                    <label for="driver">Sort by Driver:</label>
                    <select name="driver" id="driver" onchange="this.form.submit()">
                        <option value="">All</option>
                        <?php foreach ($drivers as $driver): ?>
                            <option value="<?php echo htmlspecialchars($driver); ?>" <?php if ($driver_filter == $driver) echo 'selected'; ?>><?php echo htmlspecialchars($driver); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form><br>
                <div style="overflow:auto">
                    <table>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Record Date</th>
                            <th>Record Time</th>
                            <th>Driver</th>
                            <th>Record Type</th>
                            <th>Bus Name</th>
                            <th>Bus Number</th>
                            <th>Action</th>
                        </tr>
                        <?php foreach ($detailed_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['patient_first_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['patient_last_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                                <td><?php echo htmlspecialchars($record['record_time']); ?></td>
                                <td><?php echo htmlspecialchars($record['driver']); ?></td>
                                <td><?php echo htmlspecialchars($record['record_type']); ?></td>
                                <td><?php echo htmlspecialchars($record['bus_name']); ?></td>
                                <td><?php echo htmlspecialchars($record['bus_number']); ?></td>
                                <td>
                                    <button onclick="showConfirmation(<?php echo $record['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>

        <div id="confirmationPopup" class="confirmation-popup">
            <div class="confirmation-popup-inner">
                <p>Are you sure you want to delete this record?</p>
                <button class="btn" onclick="deleteRecord()">Yes</button>
                <button class="btn" onclick="closeConfirmation()">No</button>
            </div>
        </div>

        <script>
            function showConfirmation(recordId) {
                var popup = document.getElementById('confirmationPopup');
                popup.style.display = 'flex'; 
                popup.dataset.recordId = recordId; 
            }

            function closeConfirmation() {
                var popup = document.getElementById('confirmationPopup');
                popup.style.display = 'none'; 
                popup.removeAttribute('data-record-id'); 
            }

            function deleteRecord() {
                var recordId = document.getElementById('confirmationPopup').dataset.recordId;
                window.location.href = 'transport.php?confirmDelete=true&recordId=' + recordId;
            }
        </script>
    </main>
    <footer>
        <p>© Business All Rights Reserved.</p>
    </footer>
 <script src="script.js"></script>
</body>
</html>
