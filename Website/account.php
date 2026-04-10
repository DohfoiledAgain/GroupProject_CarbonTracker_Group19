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
        <h1 style="font-size: 50px; margin-bottom: -15px;">Hello<?php echo isset($_SESSION['user_id']) ? " " . $user['Username'] : ""; ?></h1>
        <p style="font-size: 30px;"><?php echo $user ? ' ' : 'Sign up or sign in to view you account'; ?></p>
    </div>

    <div class="account-container" style="padding-top: 230px;">
      <form method="POST">
        <button type="submit" name="signin" style="<?php echo $user ? 'display:none' : 'display:block'; ?>">Sign In</button>
        <button type="submit" name="signup" style="<?php echo $user ? 'display:none' : 'display:block'; ?>">Sign Up</button>
        <button type="submit" name="logout" style="<?php echo !$user ? 'display:none' : 'display:block'; ?>">Logout</button>
      </form>
    </div>
    
    <script src="functions.js"></script>
</body>
</html>