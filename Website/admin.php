<!DOCTYPE html>
<html lang="en">
  <?php
    include("components/head.php");

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 1) {
        header("Location: index.php");
        exit();
    }
  ?>

    <!-- body -->
    <div class="welcome-container">
        <h1>Admin dashboard</h1>
        <br><br>
    </div>


    <div class="admin-container">

        <p class="activity-month-header">All Users</p>
        <?php DisplayAllUsers(); ?>
        
    </div>


    <?php
      include("components/footer.php");
    ?>