<div id="login-overlay" style="<?php echo !$user ? 'display:flex' : 'display:none'; ?>">
  <div class="content">
    <h1 style="font-family: Glacial Indifference Bold">You are not signed in</h1>
    <form method="POST" style="display:flex;  flex-direction: column; align-items: center;">
      <input type="submit" class="sign-in-button" id="signin-redir" name="signin-redir" value="Sign In">
      <p style="font-size: 15px"</p>
      <input type="submit" class="sign-up-button" style="width: 311.6px" id="signup-redir" name="signup-redir" value="Sign Up"><p></p>
      <input type="submit" class="about-us-button" style="font-style: italic;" id="about-us-redir" name="about-us-redir" value="About Us">
    </form>
  </div>
</div>

<script src="js/loginPromptDisplay.js"></script>