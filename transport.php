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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteRecord'])) {
    $recordId = $_POST['recordId'];

    // Prepare and execute SQL to delete record
    $stmt = $conn->prepare("DELETE FROM detailed_records WHERE id = ?");
    $stmt->bind_param("i", $recordId);

    if ($stmt->execute()) {
        $message = "Record deleted successfully.";
    } else {
        $message = "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
}

// Construct SQL query based on record type filter
$sql = "SELECT * FROM detailed_records";
if ($record_type_filter) {
    $sql .= " WHERE record_type = '" . $conn->real_escape_string($record_type_filter) . "'";
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $detailed_records[] = $row;
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
    <title>Admin page</title>
    <link rel="stylesheet" href="style.css">
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
            <a href="generate.html">Generate report</a>
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
                <h3>Admin dashboard</h3>
                <ul>
                    <li><a href="admin.php">Company records</a></li>
                    <li><a href="transport.php">Transport records</a></li>
                    <li><a href="generate.html">Generate transport record</a></li>
                    <li><a href="insert.php">Insert transport record</a></li>
                    <li><a href="patient.php">Create patient record</a></li>
                    <li><a href="create_driver.php">Create Driver Record</a></li>
                    <li><a href="create_bus.php">Create Bus Record</a></li>
                    <li>
            <a href="logout.php">Log Out</a></li>
                </ul>
            </div>
            <div class="divideAdmin1">
                <h3>Transportation records</h3>
                <form method="get" action="">
                    <label for="record_type">Sort by Record Type:</label>
                    <select name="record_type" id="record_type" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="Pick-up" <?php if ($record_type_filter == 'Pick-up') echo 'selected'; ?>>Pick Up</option>
                        <option value="Drop-off" <?php if ($record_type_filter == 'Drop-off') echo 'selected'; ?>>Drop Off</option>
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
                                    <form method="post">
                                        <input type="hidden" name="recordId" value="<?php echo htmlspecialchars($record['id']); ?>">
                                        <input type="submit" name="deleteRecord" value="Delete" id="submit">
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
