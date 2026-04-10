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
    
    if (isset($_POST['signup_username'])) {
      // signup form was submitted
      $username = $_POST['signup_username'];
      $email = $_POST['signup_email'];
      $password = $_POST['signup_password'];
      $db = dbConnect();

      if(strlen($username) > 2){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
          if(strlen($password) > 8){
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO User (Username, Password_Hash, Email, Num_Of_Household_Members) VALUES (:username, :password, :email, :members)");
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':members', 1);
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
      $usernameemail = $_POST['signin_username_email'];
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

  <div id="sign-up-in">
    <div id="sign-up-container" style="<?php echo $show === 'sign-up' ? 'display:block' : 'display:none'; ?>">
      <h1>Sign Up</h1>

      <form method="POST" action="login.php">
        <p class="error-text"><?php echo $signuperror; ?></p>
        <div class="textbox">
          <label for="signup-username-tb">Create Username: </label><br>
          <input type="text" id="signup-username-tb" name="signup_username" autocomplete="off">
        </div>

        <div class="textbox">
          <label for="signup-email-tb">Enter Email: </label><br>
          <input type="text" id="signup-email-tb" name="signup_email" autocomplete="email">
        </div>

        <div class="textbox">
          <label for="signup-password-tb">Create Password: </label><br>
          <input type="password" id="signup-password-tb" name="signup_password" autocomplete="new-password">
        </div>   

        <input type="submit" id="signup-submit", value="Sign Up">
      </form>
      <a href="login.php?sign-in">Already Have an Account</a>
    </div>
    <div id="sign-in-container" style="<?php echo $show === 'sign-in' ? 'display:block' : 'display:none'; ?>">
      <h1>Sign In</h1>
      <form method="POST" action="login.php">
        <p class="error-text"><?php echo $signinerror; ?></p>
        <div class="textbox">
          <label for="signin-username-email-tb">Enter Username or Email: </label><br>
          <input type="text" id="signin-username-email-tb" name="signin_username_email" autocomplete="email">
        </div>

        <div class="textbox">
          <label for="signin-password-tb">Enter Password: </label><br>
          <input type="password" id="signin-password-tb" name="signin_password" autocomplete="current-password">
        </div>   

        <input type="submit" id="signin-submit", value="Sign In">
      </form>
      <a href="login.php?sign-up">Create an Account</a>
    </div>
  </div>

  <script src="js/login.js"></script>

</body>
</html>