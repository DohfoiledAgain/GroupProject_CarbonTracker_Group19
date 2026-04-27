<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>

    <!-- body -->
    <div class="welcome-container">
        <h1>Activity Log</h1>
        <p>Track your activity</p>
    </div>

    
    <div class="activity-log-container">

        <?php DisplayActivityLog(); ?>
        
    </div>
    
    <script>
          
    </script>

    <?php
      include("components/loginprompt.php");
      include("components/footer.php");
    ?>