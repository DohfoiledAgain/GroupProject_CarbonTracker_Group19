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

            <!-- navbar items -->
            <ul class="navbar-items">

                <li class="navbar-logo">
                    <a href="index.php">🍃 CO2 Tracker</a>
                </li>

                <li class="menu-button">
                    <a href="index.php">Menu &nbsp☰</a>
                </li>

            </ul>
        </nav>
    </header>

    <script>
        
        // space for navbar js

    </script>
</body>