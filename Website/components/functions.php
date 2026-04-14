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


  /* ------------------ Activity log functions */
  function DisplayThisMonthsActivities($selected_month_datetime) {
    $db = dbConnect();
    $month = $selected_month_datetime->format('m-Y'); 
       
    $stmt = $db->prepare("
        SELECT AL.*, U.Username, C.Name, C.Unit, C.Emission_Unit
        FROM Activity_Log AL
        INNER JOIN User U ON AL.User_ID = U.User_ID
        INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
        WHERE U.User_ID = :uid 
        AND substr(Activity_Date, 4, 7) = :selected_month
        ORDER BY AL.Activity_Date ASC
    ");
    $stmt->bindValue(':uid', $_SESSION['user_id']);
    $stmt->bindValue(':selected_month', $month);

    $results = $stmt->execute();

    if ($results)
    {
        $lastDate = "";

        echo "
        <table class='activities-table'>
        <tr>
            <th>Date</th>
            <th>Category</th>
            <th>Value</th>
            <th>Notes</th>
            <th>CO2 Emissions</th>
        </tr>";
        
        while ($row = $results->fetchArray())
        {
            $currentDate = $row['Activity_Date'];
            $dateTime = DateTime::createFromFormat('d-m-Y', $currentDate);
            $dateFormatted = $dateTime->format('jS');

            // if the date has changed since the last row, print the new header
            if ($currentDate !== $lastDate) {
                echo "
                <tr></tr>
                <tr></tr>
                <tr></tr>
                <tr class='date-break'>
                    <td colspan='5'><p>" . $dateFormatted . "</p></td>
                </tr>";
                $lastDate = $currentDate;
            }

            echo "
            <tr>
                <td>{$row['Activity_Date']}</td>
                <td>{$row['Name']}</td>
                <td>{$row['Value']} {$row['Unit']}</td>
                <td>{$row['Notes']}</td>
                <td>{$row['Calculated_Emissions']} {$row['Emission_Unit']}</td>
            </tr>";
        }
        echo "</table>";
    }
    else {
        echo "No activities found or query failed.";
    }
  }


  function DisplayMonthSelect() {
      // get current month
      $current_month_datetime = new DateTime('first day of this month 00:00:00');
      $current_m_y = $current_month_datetime->format('m-y');
      
      // get selected month from URL, default to current date if unset
      $selected_month_param = isset($_GET['date']) ? $_GET['date'] : $current_m_y; // in m-y format
      $selected_month_datetime = DateTime::createFromFormat('m-y', $selected_month_param);
      $selected_month_datetime->modify('first day of this month 00:00:00');
      
      // checks that selected month is not in the future
      if ($selected_month_datetime > $current_month_datetime) {
        header("Location: activity-log.php?date=" . $current_m_y);
        exit;
      }

      $prev_month = (clone $selected_month_datetime)->modify('-1 month')->format('m-y');
      $next_month = (clone $selected_month_datetime)->modify('+1 month')->format('m-y');

      echo "<div class='activity-month-select-container'>";
          echo "<a href='?date=$prev_month' class='month-select-arrow'>❮</a>";
          echo "<p class='activity-month-header'>" . $selected_month_datetime->format('F Y') . "</p>";
          if ($selected_month_datetime < $current_month_datetime) {
            echo "<a href='?date=$next_month' class='month-select-arrow'>❯</a>";
          }
          else {
            echo "<p> </p>";
          }
      echo "</div>";
      
      if ($selected_month_datetime < $current_month_datetime) {
        echo "<div class='month-select-today'><a href='?date=" . $current_m_y . "'>Jump to now</a></div>";
      }

      DisplayThisMonthsActivities($selected_month_datetime);
  }

?>

<!-- ------------------ Signup/in functions -->
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
        } else if ($_GET['signup-error'] == "invalid_fname") {
            $signuperror = "First name must be longer than 1 characters";
        } else if ($_GET['signup-error'] == "invalid_lname") {
            $signuperror = "Second name must be longer than 1 characters";
        }
    }
    
    if (isset($_POST['signup_username'])) {
      // signup form was submitted
      $fname = $_POST['signup_fname'];
      $lname = $_POST['signup_lname'];
      $username = $_POST['signup_username'];
      $email = strtolower($_POST['signup_email']);
      $password = $_POST['signup_password'];
      $members = $_POST['signup_household'];
      $db = dbConnect();
      if(strlen($fname) > 1){
        if(strlen($lname) > 2){
          if(strlen($username) > 2){
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
              if(strlen($password) > 8){
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO User (Fname, Lname, Username, Password_Hash, Email, Num_Of_Household_Members) VALUES (:fname, :lname, :username, :password, :email, :members)");              
                $stmt->bindValue(':fname', $fname);
                $stmt->bindValue(':lname', $lname);
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
        } else {
          header("Location: login.php?sign-up&signup-error=invalid_lname");
        }
      } else {
        header("Location: login.php?sign-up&signup-error=invalid_fname");
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