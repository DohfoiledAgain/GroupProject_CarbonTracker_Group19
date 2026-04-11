<?php
  if (isset($_POST['about-us-redir'])) {
      header("Location: about-us.php");
      exit();
  }
?>

<div id="login-overlay" style="<?php echo !$user ? 'display:flex' : 'display:none'; ?>">
  <div class="content">
    <h1>You are not signed in</h1>
    <form method="POST" style="display:flex;  flex-direction: column; align-items: center;">
      <input type="submit" class="sign-in-button" id="signin-redir" name="signin-redir" value="Sign In">
      <input type="submit" class="sign-up-button" id="signup-redir" name="signup-redir" value="Sign Up"><p></p>
      <input type="submit" class="about-us-button" id="about-us-redir" name="about-us-redir" value="About Us">
    </form>
  </div>
</div>

<script src="js/loginPromptDisplay.js"></script>