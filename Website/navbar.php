<!DOCTYPE html>
<html lang="en">
<head>
  <title>Carbon Footprint Tracker</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="navbar.css" />
</head>

<body class="bg-color">

    <?php

        require_once("functions.php");

        // when the login button is clicked
        if(isset($_POST['login']))
        {
            header("Location: " . 'login.php'); 
        }

    ?>

    <!-- navbar frontend -->
    <header>
        <nav class="navbar">

            <!-- svg leaf icon -->
            <svg viewBox="0 0 25000 25000">
	            <symbol id="leaf" viewBox="-0.1 -0.1 3.2 3.2">
		            <path stroke-width="0.2" d="m0,0 h1 a2,2 0,0,1 2,2 v1 h-1 a2,2 0,0,1 -2,-2z"/>
	            </symbol>
	            <use xlink:href="#leaf" x="100" y="100" width="620" height="620" stroke="darkgreen" fill="white"/>
            </svg>

            <!-- navbar items -->
            <ul class="navbar-items">

                <li class="navbar-logo">
                    <a href="index.php">CO2 Tracker</a>
                </li>

                <li>
                    <a href="index.php">Menu &nbsp☰</a>
                </li>

            </ul>
        </nav>
    </header>

    <script>
        
        // space for navbar js

    </script>
</body>