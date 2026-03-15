<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Footprint Tracker</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <!-- navbar -->
    <?php
    require_once("functions.php");
    include("navbar.php");
    ?>

    <!-- body -->
    <br><br><br>
    <div class="welcome-container">
        <h1 style="font-size: 50px; margin-bottom: -15px;">Dashboard</h1>
        <p style="font-size: 30px;">Your CO2 stats</p>
    </div>
    

    <div class="dashboard-container" style="padding-top: 180px;">
        <h1 style="font-size: 30px;">this is where graphs go</h1>

        <p style="font-size: 22px;">and suggestions to reduce carbon</p>
    </div>
    
    <script src="functions.js"></script>
</body>
</html>