<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'driver') {
    header("Location: index.php");
    exit;
}


?><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <link rel="shortcut icon" href="images/logo.png">
    <title>Driver Page</title>
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
<div style="padding: 20px;height: 700px;">
   <div >
    <h3>Driver Records</h3>
    <p>Record for pick-up and drop-off</p>
<div style="overflow: auto;">
    <table id="driverTable">
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>No of patients</th>
            <th>Record type</th>
            <th>Bus number</th>
        </tr>
        <tr>
            <td>2/4/2024</td>
            <td>5:30pm</td>
            <td>7</td>
            <td>Pickup</td>
            <td>GF54657468</td>
        </tr>
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