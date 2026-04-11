<!DOCTYPE html>
<html lang="en">
  <?php
    include("components/head.php");
    if (isset($_POST['signin'])) {
      header("Location: login.php?sign-in");
      exit();
    }
    if (isset($_POST['signup'])) {
      header("Location: login.php?sign-up");
      exit();
    }
    if (isset($_POST['logout'])) {
      session_destroy();
      header("Location: login.php?sign-in");
      exit();
    }
  ?>
<body>
    <!-- navbar -->
    <?php
    include("components/navbar.php");
    ?>

    <!-- body -->
    <div class="welcome-container">
        <br>
        <h1 style="font-size: 50px; margin-bottom: -15px;">My account</h1>
        <p style="font-size: 30px;"><?php echo isset($_SESSION['user_id']) ? "Currently signed in as: " . $user['Username'] . "" : "Sign in to start tracking your emissions!"; ?></p>
        <form method="POST">
            <button type="submit" name="logout" class="sign-up-button" style="<?php echo !$user ? 'display:none' : 'display:block'; ?>">Logout</button>
        </form>
        <br><br>
    </div>

    <div class="account-container">
        <p style="font-family: Glacial Indifference Bold; font-size: 35px;">Account Details</p>
        <p style="font-family: Glacial Indifference Bold; font-size: 26px;">Email:</p>
        <p style="font-family: Glacial Indifference; font-size: 26px; margin-top: -15px;"><?php echo isset($_SESSION['user_id']) ? $user['Email'] : ""; ?></p>
        <p style="font-family: Glacial Indifference Bold; font-size: 26px;">People in household:</p>
        <p style="font-family: Glacial Indifference; font-size: 26px; margin-top: -15px;">
        <?php 
            if (isset($_SESSION['user_id']) && isset($user['Num_Of_Household_Members'])) {
                echo $user['Num_Of_Household_Members'] . ($user['Num_Of_Household_Members'] == 1 ? " person" : " people");
            } 
        ?>
        </p>
    </div>

    
    <script src="functions.js"></script>
</body>
</html>