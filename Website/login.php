<!DOCTYPE html>

<html lang="en">
  <?php
    include("components/head.php");
    $signuperror = "";
    $signinerror = "";
    if (array_key_exists('sign-in', $_GET)) {
    $show = 'sign-in';
    } else {
        $show = 'sign-up';
    }
    if (isset($_GET['signin-error'])) {
        if ($_GET['signin-error'] == "invalid_credentials") {
            $signinerror = "Invalid username/email or password.";
        }
    }
    if (isset($_GET['signup-error'])) {
        if ($_GET['signup-error'] == "invalid_username") {
            $signuperror = "Username must be longer than 2 characters";
        } else if ($_GET['signup-error'] == "invalid_password") {
            $signuperror = "Password must be longer than 8 characters";
        } else if ($_GET['signup-error'] == "invalid_email") {
            $signuperror = "Email is invalid";
        }
    }

    // signup button clicked
    if (isset($_POST['signup-redir'])) {
        header("Location: login.php?sign-up");
        exit();
    }
    // sign in button clicked
    if (isset($_POST['signin-redir'])) {
        header("Location: login.php?sign-in");
        exit();
    }
    
    if (isset($_POST['signup_username'])) {
      // signup form was submitted
      $username = $_POST['signup_username'];
      $email = strtolower($_POST['signup_email']);
      $password = $_POST['signup_password'];
      $members = $_POST['signup_household'];
      $db = dbConnect();

      if(strlen($username) > 2){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
          if(strlen($password) > 8){
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO User (Username, Password_Hash, Email, Num_Of_Household_Members) VALUES (:username, :password, :email, :members)");
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':members', $members);
            $stmt->execute();

            $_SESSION['user_id'] = $db->lastInsertRowID();
            header("Location: index.php");
          } else {
            header("Location: login.php?sign-up&signup-error=invalid_password");
          }
        } else {
          header("Location: login.php?sign-up&signup-error=invalid_email");
        }
      } else {
        header("Location: login.php?sign-up&signup-error=invalid_username");
      }
      exit();
    }

    if (isset($_POST['signin_username_email'])) {
        // signin form was submitted
      $usernameemail = strtolower($_POST['signin_username_email']);
      $password = $_POST['signin_password'];
      $db = dbConnect();

      $stmt = $db->prepare("SELECT * FROM User WHERE Username = :usernameemail OR Email = :usernameemail");
      $stmt->bindValue(':usernameemail', $usernameemail);
      $result = $stmt->execute();
      $user = $result->fetchArray(SQLITE3_ASSOC);

      if (password_verify($password, $user['Password_Hash'])){
        $_SESSION['user_id'] = $user['User_ID'];
        header("Location: index.php");
      } else {
        header("Location: login.php?sign-in&signin-error=invalid_credentials");
      }

      exit();
    }
  ?>
<body>
    <!-- navbar -->
    <?php
    include("components/navbar.php");
    ?>


    <!-- body -->

    <div style="display: flex; width: 100%; height: 100%; margin: 0px; left: 0px; position: absolute; top: 0px; z-index: -1;">

        <div class="sign-up-in-left-container" style="padding-top: 10px;">

            <div id="sign-up-in">
                <!-- if sign UP was selected, display the sign up form -->
                <div id="sign-up-container" style="<?php echo $show === 'sign-up' ? 'display:block' : 'display:none'; ?>">
                    <h1 style="font-family: 'Glacial Indifference Bold'; font-size: 40px;">Create an Account</h1>

                    <form method="POST" action="login.php">
                        <p class="error-text"><?php echo $signuperror; ?></p>
                        <div class="textbox">
                            <input type="text" class="account-input-form" id="signup-username-tb" name="signup_username" autocomplete="off" placeholder="Username">
                        </div>
                        <br>
                        <div class="textbox">
                            <input type="text" class="account-input-form" id="signup-email-tb" name="signup_email" autocomplete="email" placeholder="Email">
                        </div>
                        <br>
                        <div class="textbox">
                            <input type="password" class="account-input-form" id="signup-password-tb" name="signup_password" autocomplete="new-password" placeholder="Password">
                        </div>
                        <br>
                        <div class="textbox">
                            <input type="number" max="15" min="1" class="account-input-form" id="signup-household-tb" name="signup_household" autocomplete="off" placeholder="Household size">
                        </div>  
                        <br><br>
                        <input type="submit" class= "sign-in-button" id="signup-submit", value="Sign Up">
                    </form>
                </div>

                <!-- if sign IN was selected, display the sign in form -->
                <div id="sign-in-container" style="<?php echo $show === 'sign-in' ? 'display:block' : 'display:none'; ?>">
                    <h1 style="font-family: 'Glacial Indifference Bold'; font-size: 40px;">Sign in to Your Account</h1>
                    <form method="POST" action="login.php">
                        <p class="error-text"><?php echo $signinerror; ?></p>
                        <div class="textbox">
                            <!-- <label for="signin-username-email-tb">Enter Username or Email: </label><br> -->
                            <input type="text" class="account-input-form" id="signin-username-email-tb" name="signin_username_email" autocomplete="email" placeholder="Username or Email">
                        </div>

                        <div class="textbox">
                            <!-- <label for="signin-password-tb">Enter Password: </label><br> -->
                            <br>
                            <input type="password" class="account-input-form" id="signin-password-tb" name="signin_password" autocomplete="current-password" placeholder="Password">
                        </div>   
                        <br><br>
                        <input type="submit" class="sign-in-button" id="signin-submit", value="Sign In">
                    </form>
                </div>
            </div>
            
        </div>


        <div class="account-side-container">
            <!-- if sign IN was selected, display the sign UP side container -->
            <div style="<?php echo $show === 'sign-in' ? 'display:block' : 'display:none'; ?>">
                <h1 style="font-family: 'Glacial Indifference'; color: white;">New here?</h1>
                <p style="font-family: 'Glacial Indifference'; color: white; font-size: 24px;">Create an account to start tracking your carbon emissions and make a positive impact on the environment!</p>
                <br>
                <form method="POST">
                    <input type="submit" class="sign-up-button" id="signup-redir" name="signup-redir" value="Sign Up">
                    <!--    
                    <button type="submit" name="signin" class="sign-in-button" style="<?php echo $user ? 'display:none' : 'display:block'; ?>">Sign In</button>
                    <button type="submit" name="signup" class="sign-up-button" style="background-color: white !important; color: black; <?php echo $user ? 'display:none' : 'display:block'; ?>">Sign Up</button>
                    <button type="submit" name="logout" class="sign-in-button" style="<?php echo !$user ? 'display:none' : 'display:block'; ?>">Logout</button>
                    -->
                </form>
            </div>

            <!-- if sign UP was selected, display the sign IN side container -->
            <div style="<?php echo $show === 'sign-up' ? 'display:block' : 'display:none'; ?>">
                <h1 style="font-family: 'Glacial Indifference'; color: white;">Already have an account?</h1>
                <br>
                <form method="POST">
                    <input type="submit" class="sign-up-button" id="signin-redir" name="signin-redir" value="Sign In">
                </form>
            </div>
        </div>
        
    </div>

  <script src="js/login.js"></script>

</body>
</html>