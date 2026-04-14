<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>

    <!-- body -->
    <div class="welcome-container">
        <h1 style="font-size: 50px; margin-bottom: -15px;">Dashboard</h1>
        <p style="font-size: 30px;">Your CO2 stats</p>
    </div>
    

    <div class="dashboard-container" style="padding-top: 230px; font-size: 22px;">
        <h1 style="font-size: 30px;">All activities (temporary)</h1>

        <?php DisplayAllActivities();?>
    </div>
    
    <?php
      include("components/loginprompt.php");
    ?>
    <script src="js/functions.js"></script>
</body>
</html>