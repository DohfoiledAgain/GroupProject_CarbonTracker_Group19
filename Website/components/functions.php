<?php
session_start();

/* ------------------------------------ FORM CHECKS ------------------*/

/* -------- Sign up / Sign in Form Checks */

if (isset($_POST['signup-redir'])) {
  header("Location: login.php?sign-up");
  exit();
}
if (isset($_POST['signin-redir'])) {
  header("Location: login.php?sign-in");
  exit();
}
if (isset($_POST['signup_username'])) {
  $fname = $_POST['signup_fname'];
  $lname = $_POST['signup_lname'];
  $username = $_POST['signup_username'];
  $email = strtolower($_POST['signup_email']);
  $password = $_POST['signup_password'];
  $members = $_POST['signup_household'];
  $db = dbConnect();

  if ($fname) {
    if ($lname) {
      if (strlen($username) > 2) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          if (strlen($password) > 8) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO User (Fname, Lname, Username, Password_Hash, Email, Num_Of_Household_Members) VALUES (:fname, :lname, :username, :password, :email, :members)");
            $stmt->bindValue(':fname', $fname, SQLITE3_TEXT);
            $stmt->bindValue(':lname', $lname, SQLITE3_TEXT);
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':members', $members, SQLITE3_INTEGER);
            try {
              $stmt->execute();
              $_SESSION['user_id'] = $db->lastInsertRowID();
              header("Location: index.php");
            } catch (Exception $e) {
              $errorMessage = $e->getMessage();
              if (stripos($errorMessage, 'UNIQUE') !== false) {
                if (stripos($errorMessage, 'Username') !== false) {
                  header("Location: login.php?sign-up&signup-error=username_taken");
                } elseif (stripos($errorMessage, 'Email') !== false) {
                  header("Location: login.php?sign-up&signup-error=email_taken");
                } else {
                  header("Location: login.php?sign-up&signup-error=duplicate_entry");
                }
              } else {
                header("Location: login.php?sign-up&signup-error=db_error");
              }
            }
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
  $usernameemail = $_POST['signin_username_email'];
  $usernameemaillower = strtolower($usernameemail);
  $password = $_POST['signin_password'];
  $db = dbConnect();

  $stmt = $db->prepare("SELECT * FROM User WHERE Username = :usernameemail OR Username = :usernameemaillower OR Email = :usernameemail OR Email = :usernameemaillower");
  $stmt->bindValue(':usernameemail', $usernameemail, SQLITE3_TEXT);
  $stmt->bindValue(':usernameemaillower', $usernameemaillower, SQLITE3_TEXT);
  $result = $stmt->execute();
  $user = $result->fetchArray(SQLITE3_ASSOC);

  if ($user && password_verify($password, $user['Password_Hash'])) {
    $_SESSION['user_id'] = $user['User_ID'];
    header("Location: index.php");
  } else {
    header("Location: login.php?sign-in&signin-error=invalid_credentials");
  }
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
    $signuperror = "Must include first name";
  } else if ($_GET['signup-error'] == "invalid_lname") {
    $signuperror = "Must include second name";
  } else if ($_GET['signup-error'] == "username_taken") {
    $signuperror = "Username already in use";
  } else if ($_GET['signup-error'] == "email_taken") {
    $signuperror = "Email already in use";
  } else if ($_GET['signup-error'] == "duplicate_entry") {
    $signuperror = "Shouldn't happen, fallback for non username/email reasons";
  } else if ($_GET['signup-error'] == "db_error") {
    $signuperror = "UH OH PANIC! UH OH PANIC! UH OH PANIC! UH OH PANIC! UH OH PANIC! UH OH PANIC!";
  }
}

/* ------------ Activity Log Form Checks */

if (isset($_POST['action'])) {
  $db = dbConnect();
  $action = $_POST['action'];
  $redirect = $_SERVER['HTTP_REFERER'] ?? 'activity-log.php';

  if ($action === 'delete_activity') {
    $stmt = $db->prepare("DELETE FROM Activity_Log WHERE Log_ID = :id AND User_ID = :user_id");
    $stmt->bindValue(':id', $_POST['log_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: " . $redirect);
    exit();
  }

  if ($action === 'edit_activity') {
    $inputdate = $_POST['activity_date'];
    $formatteddate = date("d-m-Y", strtotime($inputdate));
    $log_id = $_POST['log_id'];
    $value = $_POST['value'];

    $stmt = $db->prepare("SELECT Category_ID FROM Activity_Log WHERE Log_ID = :id AND User_ID = :user_id");
    $stmt->bindValue(':id', $log_id, SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_NUM);
    $cat = $row[0];

    $stmt = $db->prepare("SELECT Emission_Factor FROM Emission_Category WHERE Category_ID = :cat");
    $stmt->bindValue(':cat', $cat, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $emiss_factor = $row['Emission_Factor'];
    if ($emiss_factor) {
        $calc_emiss = $value * $emiss_factor;
    } else {
    die("Error: Activity Log entry not found for this user.");
}

    $stmt = $db->prepare("UPDATE Activity_Log SET Activity_Date = :date, Value = :value, Notes = :notes, Calculated_Emissions = :calc_emiss WHERE Log_ID = :id AND User_ID = :user_id");
    $stmt->bindValue(':date', $formatteddate, SQLITE3_TEXT);
    $stmt->bindValue(':value', $value, SQLITE3_FLOAT);
    $stmt->bindValue(':notes', $_POST['notes'], SQLITE3_TEXT);
    $stmt->bindValue(':calc_emiss', $calc_emiss, SQLITE3_FLOAT);
    $stmt->bindValue(':id', $_POST['log_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: " . $redirect);
    exit();
  }

  if ($action === 'add_activity') {
    $inputdate = $_POST['activity_date'];
    $formatteddate = date("d-m-Y", strtotime($inputdate));

    $value = $_POST['value'];
    $stmt = $db->prepare("SELECT Emission_Factor FROM Emission_Category WHERE Category_ID = :cat");
    $stmt->bindValue(':cat', $_POST['category_id'], SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    $emiss_factor = $row['Emission_Factor'];
    if ($emiss_factor) {
        $calc_emiss = $value * $emiss_factor;
    }

    $stmt = $db->prepare("INSERT INTO Activity_Log (User_ID, Category_ID, Activity_Date, Value, Notes, Calculated_Emissions) VALUES (:user_id, :cat, :date, :value, :notes, :calc_emiss)");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':cat', $_POST['category_id'], SQLITE3_TEXT);
    $stmt->bindValue(':date', $formatteddate, SQLITE3_TEXT);
    $stmt->bindValue(':value', $value, SQLITE3_FLOAT);
    $stmt->bindValue(':notes', $_POST['notes'], SQLITE3_TEXT);
    $stmt->bindValue(':calc_emiss', $calc_emiss, SQLITE3_FLOAT);
    $stmt->execute();
    header("Location: " . $redirect);
    exit();
  }
}



/* ------------------------------------ FUNCTIONS ------------------*/

/* ------------------ Terms and Conditions functions */

function checkTC()
{
  if (!isset($_SESSION["tcAccepted"])) {
    include("components/termsconditions.php");
    exit();
  }
}

function showTC()
{
  include("components/termsconditions.php");
  exit();
}


/* ------------------ Database functions */

function dbConnect()
{
  $db = new SQLite3(__DIR__ . "/data.db");
  $db->enableExceptions(true);
  return $db;
}

function getUser()
{
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

function Temp()
{
  $db = dbConnect();
  $results = $db->query(
    "SELECT *
    FROM Activity_Log
    Activity_Date ASC"
  );
}


/* ------------------ Activity log functions */

function DisplayActivityLog()
{
  // determine current month and selected month
  $current_month_datetime = new DateTime('first day of this month 00:00:00');
  $current_m_y = $current_month_datetime->format('m-y');

  $selected_month_param = isset($_GET['date']) ? $_GET['date'] : $current_m_y;
  $selected_month_datetime = DateTime::createFromFormat('m-y', $selected_month_param);
  $selected_month_datetime->modify('first day of this month 00:00:00');

  if ($selected_month_datetime > $current_month_datetime) {
    header("Location: activity-log.php?date=" . $current_m_y);
    exit;
  }

  $prev_month = (clone $selected_month_datetime)->modify('-1 month')->format('m-y');
  $next_month = (clone $selected_month_datetime)->modify('+1 month')->format('m-y');

  // display month title and navigation arrows
  echo "<div class='activity-month-select-container'>";
  echo "<a href='?date=$prev_month' class='month-select-arrow'>❮</a>";
  echo "<p class='activity-month-header'>" . $selected_month_datetime->format('F Y') . "</p>";
  if ($selected_month_datetime < $current_month_datetime) {
    echo "<a href='?date=$next_month' class='month-select-arrow'>❯</a>";
  } else {
    echo "<p> </p>";
  }
  echo "</div>";

  if ($selected_month_datetime < $current_month_datetime) {
    echo "<div class='month-select-today'><a href='?date=" . $current_m_y . "'>Jump to now</a></div>";
  }

  // display log for this month
  DisplayThisMonthsActivities($selected_month_datetime);
}

function DisplayThisMonthsActivities($selected_month_datetime)
{
  $db = dbConnect();
  $month = $selected_month_datetime->format('m-Y');

  // retrieve database values
  $stmt = $db->prepare("
          SELECT AL.*, U.Username, C.Name, C.Unit, C.Emission_Unit
          FROM Activity_Log AL
          INNER JOIN User U ON AL.User_ID = U.User_ID
          INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
          WHERE U.User_ID = :user_id 
          AND substr(Activity_Date, 4, 7) = :selected_month
          ORDER BY AL.Activity_Date ASC
      ");
  $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
  $stmt->bindValue(':selected_month', $month, SQLITE3_TEXT);
  $results = $stmt->execute();

  if ($results) {
    $lastDate = "";
    echo "
          <table class='activities-table'>
          <tr>
              <th>Date</th>
              <th>Category</th>
              <th>Value</th>
              <th>Notes</th>
              <th>CO2 Emissions</th>
              <th>Actions</th>
          </tr>";
    
    // display table contents
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
      $currentDate = $row['Activity_Date'];
      $dateTime = DateTime::createFromFormat('d-m-Y', $currentDate);
      $dateFormatted = $dateTime->format('jS');

      if ($currentDate !== $lastDate) {
        echo "
                  <tr></tr>
                  <tr></tr>
                  <tr></tr>
                  <tr class='date-break'>
                      <td colspan='6'><p>" . $dateFormatted . "</p></td>
                  </tr>";
        $lastDate = $currentDate;
      }

      $logId = $row['Log_ID'];
      $escapedNotes = htmlspecialchars($row['Notes'], ENT_QUOTES);
      $escapedValue = htmlspecialchars($row['Value'], ENT_QUOTES);
      $escapedDate  = htmlspecialchars($row['Activity_Date'], ENT_QUOTES);
      $dateForEditInput = $dateTime->format('Y-m-d');

      echo "
              <tr id='row-{$logId}'>
                  <td>{$row['Activity_Date']}</td>
                  <td>{$row['Name']}</td>
                  <td>{$row['Value']} {$row['Unit']}</td>
                  <td>{$row['Notes']}</td>
                  <td>{$row['Calculated_Emissions']} {$row['Emission_Unit']}</td>
                  <td class='action-buttons'>
                      <button onclick='openEditModal({$logId}, \"{$dateForEditInput}\", \"{$escapedValue}\", \"{$escapedNotes}\")' class='edit-delete-button'>Edit</button>
                      <button onclick='confirmDelete({$logId})' class='edit-delete-button'>Delete</button>
                  </td>
              </tr>";
    }
    echo "</table>";

    // edit activity modal
    echo "<div class='add-activity-btn-container'><button onclick='openAddModal()' class='add-activity-btn'>+ Add Activity</button></div>";
    echo "
          <div id='editModal' style='display:none;' class='activity-modal-overlay'>
              <div class='modal-content edit-modal'>
                  <p style='text-align: center; font-family: \"Glacial Indifference Bold\"; font-size: 22px; margin: 0;'>Edit Activity</p>
                  <form method='POST' style='margin-top: -60px'>
                      <input type='hidden' name='action' value='edit_activity'><br><br>
                      <input type='hidden' name='log_id' id='edit_log_id'><br><br>
                      <label>Date: <input type='date' name='activity_date' id='edit_date' class='date-input'></label><br><br>
                      <label>Value: <input type='number' step='0.05' value='0' name='value' id='edit_value'></label><br><br>
                      <label>Notes: <input type='text' name='notes' id='edit_notes'></label><br><br>
                      <div class='button-group'>
                          <button type='submit' class='modal-button'>Save</button>
                          <button type='button' onclick='closeModal(\"editModal\")' class='modal-button'>Cancel</button>
                      </div>
                  </form>
              </div>
          </div>";

    // add activity modal
    $db2 = dbConnect();
    $cats = $db2->query("SELECT Category_ID, Name, Unit FROM Emission_Category ORDER BY Name ASC");
    $categoryOptions = "";
    while ($cat = $cats->fetchArray(SQLITE3_ASSOC)) {
      $categoryOptions .= "<option value='{$cat['Category_ID']}'>{$cat['Name']} ({$cat['Unit']})</option>";
    }

    echo "
        <div id='addModal' class='activity-modal-overlay' style='display:none;'>
            <div class='modal-content add-modal'>
                <p style='text-align: center; font-family: \"Glacial Indifference Bold\"; font-size: 22px; margin: 0;'>Add Activity</p>
                <form method='POST'> <input type='hidden' name='action' value='add_activity'><br><br>
                    <label>Date: <input type='date' class='date-input' name='activity_date'></label><br><br>
                    <label>Category: <select name='category_id'> {$categoryOptions} </select> </label><br><br>
                    <label>Value: <input type='number' step='0.05' value='0' name='value' id='edit_value'></label><br><br>
                    <label>Notes: <input type='text' name='notes'></label><br><br><br>
                    <div class='button-group'>
                        <button type='submit' class='modal-button'>Add</button>
                        <button type='button' onclick='closeModal(\"addModal\")' class='modal-button'>Cancel</button>
                    </div>
                </form>
            </div>
        </div>";

    // delete activity modal
    echo "
          <div id='deleteModal' style='display:none;' class='activity-modal-overlay'>
              <div class='modal-content delete-modal'>
                  <p>Are you sure you want to delete this activity?</p>
                  <form method='POST'>
                      <input type='hidden' name='action' value='delete_activity'>
                      <input type='hidden' name='log_id' id='delete_log_id'>
                      <div class='button-group'>
                          <button type='submit' class='modal-button'>Yes, delete</button>
                          <button type='button' onclick='closeModal(\"deleteModal\")' class='modal-button'>Cancel</button>
                      </div>
                  </form>
              </div>
          </div>";

  } else {
    echo "No activities found or query failed.";
  }
}




