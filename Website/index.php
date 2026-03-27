<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>
<body>
    <!-- navbar -->
    <?php
    include("components/navbar.php");
    ?>

    <!-- body -->
    <div class="welcome-container">
        <h1 style="font-size: 50px; margin-bottom: -15px;">Dashboard</h1>
        <p style="font-size: 30px;">Your CO2 stats</p>
    </div>
    

    <div class="dashboard-container" style="padding-top: 230px;">
        <h1 style="font-size: 30px;">this is where graphs go</h1>

        <p style="font-size: 22px;">and suggestions to reduce carbon</p>
    </div>
    
    <?php checkTC();?>
    <script src="js/functions.js"></script>
</body>
</html>