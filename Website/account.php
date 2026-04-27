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

    <!-- body -->
    <div class="welcome-container">
        <h1>My account</h1>
        <p><?php echo isset($_SESSION['user_id']) ? "Currently signed in as: " . $user['Username'] . "" : "Sign in to start tracking your emissions!"; ?></p>
        <form method="POST">
            <button type="submit" name="logout" class="sign-up-button" style="<?php echo !$user ? 'display:none' : 'display:block'; ?>">Logout</button>
        </form>
        <br><br>
    </div>

    <div class="account-container">
        <p>Account Details</p>

        <p id="account-title">First name:</p>
        <p2 id="account-info"><?php echo isset($_SESSION['user_id']) ? $user['Fname'] : ""; ?></p2>

        <p id="account-title">Last name:</p>
        <p id="account-info"><?php echo isset($_SESSION['user_id']) ? $user['Lname'] : ""; ?></p2>

        <p id="account-title">Email:</p>
        <p id="account-info"><?php echo isset($_SESSION['user_id']) ? $user['Email'] : ""; ?></p2>

        <p id="account-title">People in household:</p>
        <p id="account-info">
            <?php 
                if (isset($_SESSION['user_id']) && isset($user['Num_Of_Household_Members'])) {
                    echo $user['Num_Of_Household_Members'] . ($user['Num_Of_Household_Members'] == 1 ? " person" : " people");
                } 
            ?>
        </p>
        <br><br>

    </div>
    <?php
      include("components/footer.php");
    ?>