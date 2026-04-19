<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>

    <!-- body -->
    <div class="welcome-container">
        <h1 style="font-size: 40px; margin-bottom: -15px;">Activity Log</h1><br><br>
    </div>

    
    <div class="activity-log-container">

        <?php DisplayActivityLog(); ?>
        
    </div>
    
    <script>
          
    </script>
    <script src="js/functions.js"></script>

    <?php
      include("components/loginprompt.php");
    ?>

</body>
</html>