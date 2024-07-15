<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$data = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servername = "localhost";
$username = "root";
$password = "";
$dbname = "brightway";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST['record_date'])) {
        $record_date = $_POST['record_date'];
        $sql = "SELECT patient_first_name, patient_last_name, record_time, record_type 
                FROM detailed_records 
                WHERE record_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $record_date);
        $stmt->execute();
        $stmt->bind_result($patient_first_name, $patient_last_name, $record_time, $record_type);

        while ($stmt->fetch()) {
            $name = $patient_first_name . ' ' . $patient_last_name;
            if (!isset($data[$name])) {
                $data[$name] = array('Pick up' => '', 'Drop off' => '');
            }
            if ($record_type === 'Pick up') {
                $data[$name]['Pick up'] = $record_time;
            } else if ($record_type === 'Drop off') {
                $data[$name]['Drop off'] = $record_time;
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/logo.png">
    <script src="https://kit.fontawesome.com/f0fb58e769.js" crossorigin="anonymous"></script>
    <title>Generate Data</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table, tr, td, th {
            border: 1px solid black;
            border-collapse: collapse;padding:0;
        }
        th,td{
            background-color: #fff;text-align:center;padding:4px;font-size:13px;
        }
        #reportSection {
            display: none;
            width: auto;
            height: auto;
            background-color: #fff;
            z-index: 2;
            padding: 20px;
            overflow: auto;font-size:13px;
        }
        #topz{
            display:flex;flex-direction:row;margin-bottom:10px;flex-wrap:nowrap;font-size:13px;
        }
        .num{
            width: 40%;border:1px solid black;padding-left:10px;font-weight:500;
        }
      
        .num1{
            width: 20%;padding-left:10px;font-weight:500
        }
       
        #num2{
            border:1px solid black;
        }
    </style>
</head>
<body>
    <header id="header" style="position:sticky;top:0">
        <div class="div1">
            <img src="images/logo.png">
        </div>
        <div class="div2">
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
                <div id="list" style="background-color: #fff;">
                    <h2>Generate Daily Transportation Record</h2> 
                    <p>Enter Record date to generate transport record</p>
                    <form id="generateForm" method="POST">
                        <label for="record_date">Record date</label><br>
                        <input type="date" name="record_date" id="record_date" required><br>
                        <input type="submit" id="submit" value="Generate">
                    </form>
                </div>
            

                <section id="reportSection" <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') echo 'style="display:block"'; ?>>
        <img src="images/texas.png" alt="" width="200px">
        <div style="text-align: center;line-height:10px;font-size:14px">
            <p>Day Activity and Health Services</p>
            <h3>Daily Transportation Record</h3>
        </div>
        <div id="topz">
           <div class="num">
            <p>Name of Facility: Brightway Day Center</p>
           </div>
           <div class=num1 id="num2">
           <p>Vendor No:</p>
           </div>
           <div class=num1 id="num2">
       <p>Date:</p>
           </div>
           <div class=num1>
           <p>Page_____ of ______</p>
           </div>
    </div>
    <table id="reportTable">
    <tr>
        <th rowspan="2">No</th>
        <th rowspan="2">Individual Name</th>
        <th colspan="2">Time</th>
    </tr>
    <tr>
        <th>Pick Up</th>
        <th>Drop Off</th>
    </tr>
    <?php
    if (!empty($data)) {
        $i = 1;
        foreach ($data as $name => $times) {
            echo "<tr>";
            echo "<td>{$i}</td>";
            echo "<td>{$name}</td>";
            echo "<td>{$times['Pick up']}</td>";
            echo "<td>{$times['Drop off']}</td>";
            echo "</tr>";
            $i++;
        }
    }
    ?>
</table>
<div style="text-align:right;font-size:13px">
    <p><b>I certify that this information is true and correct:</b>____________________________________</p>
    <p style="position:relative;right:5%">Signature -driver</p>
</div>


        <button id="button1" onclick="closeReport()">Close</button>
        <button id="button2" onclick="printReport()">Print</button>
    </section>
            
            </div>  
        </div>
    </main>

    

    <script>
        function closeReport() {
            document.getElementById('reportSection').style.display = 'none';
        }

        function printReport() {
            document.getElementById('button1').style.display='none';
            document.getElementById('button2').style.display='none';
            var printContents = document.getElementById('reportSection').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    </script>
 <script src="script.js"></script>
</body>
</html>
