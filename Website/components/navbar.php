<?php

    // when the login button is clicked
    if(isset($_POST['login']))
    {
        header("Location: " . 'account.php'); 
    }

?>

<!-- navbar frontend -->
<header>
    <nav class="navbar">
        <!-- navbar items -->
        <ul class="navbar-items">

            <li class="navbar-logo">
                <a href="index.php">🍃 CO2 Tracker</a>
            </li>

            <li class="menu-button">
                <span onclick="openSideBar()">Menu &nbsp☰</span>
            </li>

        </ul>
    </nav>

</header>

<!-- sidebar frontend -->
<div id="sidebar-container" class="sidebar-container">
    <span class="close-button" onclick="closeSideBar()">&times;</span>
    <ul class="sidebar-items">
        <li style="font-size: 70px; text-align: center; justify-content: center; margin-bottom: -20px; margin-top: -30px; padding-left: 0px;"><a class="sidebar-logo" href="index.php">🍃</a><li>
        <li><a href="index.php">Dashboard</a></li>
        <hr/>
        <li><a href="activity-log.php">Activity Log</a></li>
        <hr/>
        <li><a href="about-us.php">About Us</a></li>
        <hr/>
        <li><form method="post"><button type="submit" name="login" class="account-button">Account</button></form></li>
    </ul>
</div>

<script>
    
    // space for navbar js

</script>

<br><br><br>