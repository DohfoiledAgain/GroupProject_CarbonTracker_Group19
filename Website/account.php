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
        <h1 style="font-size: 50px; margin-bottom: -15px;">Your Account</h1>
        <p style="font-size: 30px;">Sign up, log in, and view your account details.</p>
    </div>

    <div class="account-container">
    <!-- check session - if not logged in, display log in container -- if logged in, display account details and log out button -->
    <!-- if not logged in, also display a sign up button which then displays the sign up container -->
        <div class="login-container" style="padding-top: 230px;">
            <h1 style="font-size: 30px; font-family: 'Glacial Indifference'">Login</h1>

            <?php
            
            ?>
        </div>
    
        <div class="signup-container">
            <h1 style="font-size: 30px; font-family: 'Glacial Indifference'">Signup</h1>

            <?php

            ?>
        </div>

        <div class="account-details-container">
            <h1 style="font-size: 30px; font-family: 'Glacial Indifference'">Your account</h1>

            <?php

            ?>
        </div>
    </div>
    
    <script src="functions.js"></script>
</body>
</html>