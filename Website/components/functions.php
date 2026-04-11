<?php
  session_start();

  /* ------------------ Terms and Conditions functions */

  function checkTC() {
    if (!isset($_SESSION["tcAccepted"])) {
      include("components/termsconditions.php");
      exit();
    }
  }

  function showTC() {
    include("components/termsconditions.php");
    exit();
  }


  /* ------------------ Database functions */

  function dbConnect(){
    $db = new SQLite3(__DIR__ . "/data.db");
    return $db;
  }

 function getUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $db = dbConnect();
    $stmt = $db->prepare("SELECT * FROM User WHERE User_ID = :id");
    $stmt->bindValue(':id', $_SESSION['user_id']);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

  /* ------------------ Dashboard functions */

  function DisplayAllActivities() {
    $db = dbConnect();
    $results = $db->query(
        "SELECT AL.*, U.Username, C.Name, C.Unit
        FROM Activity_Log AL
        INNER JOIN User U ON AL.User_ID = U.User_ID
        INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
        ORDER BY AL.Activity_Date ASC
        ");
    // WHERE U.username = '" . $_SESSION['username'] . "'
    // ^ this should display the correct activities for the logged in user when we have a working login system ..i think  -Nick

    if ($results)
    {
        $lastDate = "";

        while ($row = $results->fetchArray())
        {
            $currentDate = $row['Activity_Date'];

            // if the date has changed since the last row, print the new header
            if ($currentDate !== $lastDate) {
                echo "<h3>" . $currentDate . "</h3>";
                $lastDate = $currentDate;
            }

            echo "<p>" . $row['Username'] . " - " . $row['Name'] . " - " . $row['Value'] . " " . $row['Unit'] . "</p>";
        }
    }
    else {
        echo "No activities found or query failed.";
    }
    
  }

?>

/* ------------------ Signup/in str_shuffle
<?php
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
      $usernameemail = $_POST['signin_username_email'];
      $usernameemaillower = strtolower($_POST['signin_username_email']);
      $password = $_POST['signin_password'];
      $db = dbConnect();

      $stmt = $db->prepare("SELECT * FROM User WHERE Username = :usernameemail OR Email = :usernameemail OR Username = :usernameemaillower OR Email = :usernameemaillower");
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