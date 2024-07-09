<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'driver') {
    header("Location: index.php");
    exit;
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
            <li style="color:white"><?php echo "Hello, " . $_SESSION['username'] . "!"?></li>
            <li><a href="driverPage.php">Home</a></li>
           <li><a href="driverinsert.php">Insert Record</a></li>
            <li> <a href="logout.php">Log Out</a></li>
            
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
        <img src="images/logo.png" alt="">
        <a href="driverPage.php">Home</a>
        <a href="driverinsert.php">Insert record</a> 

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
   <div class="divider">
    <h3>Patient List</h3>
<p>Select Patient</p>
<div>
    <table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Select</th>
        </tr>
        <tr>
            <td>Emmanuel</td>
            <td>Amadi</td>
            <td><input type="checkbox"></td>
        </tr>
    </table>
</div>
</div>  

<div class="divide2">
<form action="">
        <label for="">Date</label><br>
        <input type="date" name="pickup_date"><br>
        <label for="">Time</label><br>
        <input type="time" name="pickup_time">
        <input type="text" name="pickup_driver" hidden><br> 
        <label for="">Transport record type</label><br>
        <select name="recordType" id="">
            <option value="">Select record type</option>
            <option value="Pick-up">Pick Up</option>
            <option value="Drop-off">Drop Off</option>
        </select><br>
        <input type="text" name="pickup_bus_name" hidden>
         <label for="">Bus number</label><br>
        <input type="text" name="pickup_bus_number"><br>
        <input type="submit" id="submit">
    </form>
</div> 
</div>

</main>

<footer>
   <p>© Business All Rights Reserved.</p> 
</footer>


</body>
</html>