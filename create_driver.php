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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $driverName = $_POST['driverName'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id FROM admin WHERE name = ? OR phone = ?");
    $stmt->bind_param("ss", $driverName, $phone);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Bus with plate number '$plateNumber' already exists.";
        $_SESSION['messageType'] = 'error';
    } else {
        $role = 'driver'; 

        $stmt = $conn->prepare("INSERT INTO admin (name, phone, role, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $driverName, $phone, $role, $password);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Bus record added successfully.";
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = "Error adding bus record: " . $stmt->error;
            $_SESSION['messageType'] = 'error';
        }
    }

    $stmt->close();
    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
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
    <title>Admin page</title>
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
                <div id="list" style="background-color: #fff;">
                <?php if (!empty($message)): ?>
        <div id="notificationBar" class="notification-bar notification-<?php echo $messageType; ?>">
            <?php echo $message; ?>
            <span class="close-btn" onclick="closeNotification()">&times;</span>
        </div>
    <?php endif; ?>
                    <h2>Create Driver Record</h2> 
                    <p>Enter Driver details</p>
                    <form action="" method="post">
                        <input type="text" name="driverName" placeholder="Enter Driver Name" required><br>
                        <input type="tel" name="phone" placeholder="Enter Driver Phone" required><br>
                        <input type="text" name="password" placeholder="Set Driver Password" required><br><br>
                        <input type="submit" id="submit" value="Submit">
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>© Business All Rights Reserved.</p> 
    </footer>

    <script>
        function closeNotification() {
            var notificationBar = document.getElementById("notificationBar");
            notificationBar.style.display = "none";
        }

        // Automatically show and dismiss the notification bar after 5 seconds
        window.onload = function() {
            var notificationBar = document.getElementById("notificationBar");
            if (notificationBar) {
                notificationBar.style.display = "block";
                setTimeout(function() {
                    notificationBar.style.display = "none";
                }, 5000);
            }
        }
    </script>
 <script src="script.js"></script>
</body>
</html>
