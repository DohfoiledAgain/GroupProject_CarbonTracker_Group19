<?php
session_start();

/* ------------------------------------ FORM CHECKS ------------------*/

/* -------- Sign up / Sign in Form Checks */

if (isset($_POST['about-us-redir'])) {
      header("Location: about-us.php");
      exit();
  }

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


/* ------------ Admin Page Form Checks */

if (isset($_POST['action'])) {
  $db = dbConnect();
  $action = $_POST['action'];
  $redirect = $_SERVER['HTTP_REFERER'] ?? 'admin.php';

  if ($action === 'delete_user') {
    $stmt = $db->prepare("DELETE FROM User WHERE User_ID = :id");
    $stmt->bindValue(':id', $_POST['user_id'], SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: " . $redirect);
    exit();
  }

  if ($action === 'edit_user') {
    $user_id = $_POST['user_id'];
    $user_username = $_POST['user_username'];
    $user_email = $_POST['user_email'];
    $user_fname = $_POST['user_fname'];
    $user_lname = $_POST['user_lname'];

    $stmt = $db->prepare("
        UPDATE User
        SET Username = :username, Email = :email, Fname = :fname, Lname = :lname
        WHERE User_ID = :user_id");
    $stmt->bindValue(':username', $user_username, SQLITE3_TEXT);
    $stmt->bindValue(':email', $user_email, SQLITE3_TEXT);
    $stmt->bindValue(':fname', $user_fname, SQLITE3_TEXT);
    $stmt->bindValue(':lname', $user_lname, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
    $stmt->execute();
    header("Location: " . $redirect);
    exit();
  }
}


/* ------------ T&Cs Button Form Checks */

if (isset($_GET['show-terms'])) {
    echo '
        <div id="tc-overlay" style="display: flex !important;">
          <div class="content">
            <h1>Terms and Conditions</h1>
            <div id="scroll-box">
    
                If you continue to browse and use this website, you are agreeing to comply with and be bound by the following terms and conditions of use. If you disagree with any part of these terms and conditions, please do not use our website.
                <br>
                <ul>
                    <li>The information contained in this website is for general information purposes only. The information is provided by various trusted sources and while we endeavour to keep the information up to date and correct, we make no representations or warranties of any kind, express or implied, about the completeness, accuracy, reliability, suitability or availability with respect to the website or the information, products, services, or related graphics contained on the website for any purpose. Any reliance you place on such information is therefore strictly at your own risk.
                    </li><br>
                    <li>All emission factors used to calculate CO2 emissions are derived from the latest UK Government Greenhouse Gas (GHG) Conversion Factors (2025) published by the Department for Energy Security and Net Zero (DESNZ) and DEFRA. However we cannot ensure these factors are up to date or entirely accurate, they are simply used as a base factor to give users a general idea of their carbon usage.
                    </li><br>
                    <li>Our system to provide recommendations for reducing CO2 emissions provides you with suggestions, not instructions. Therefore we do not encourage you to do anything you are uncomfortable with or that may put you in danger. In no event will we be liable for any loss or damage concerning personal property, profits, or your health.
                    </li><br>
                    <li>From time to time this website may also include links to other websites. These links are provided for your convenience to provide further information. They do not signify that we endorse the websites. We have no responsibility for the content of the linked website(s).
                    </li><br>
                    <li>Your use of this website and any dispute arising out of such use of the website is subject to the laws of England, Northern Ireland, Scotland and Wales.
                    </li><br>
                    <li>User information is stored in our own database system and will not be shared or sold to any external organisations. Sensitive information such as passwords are encrypted with a hashing algorithm and stored in this encrypted format. The decryption key is not attainable, nor is it stored or kept in any form. Therefore, once registered, your password cannot be accessed or changed by anyone. However, we are not liable for any damages or loss of information should a data breach occur. By signing up you have understood and agreed to this risk.
                    </li><br>
                    <li>Every effort is made to keep the website up and running smoothly. However, our team takes no responsibility for, and will not be liable for, the website being temporarily unavailable due to technical issues beyond our control.
                    </li><br>
                </ul>
                Copyright © 2026 CO2 Tracker. All rights reserved.

            </div>
            <br>
            <button id="tc-accept-btn" onclick="closeTC()">Close</button>
          </div>
        </div>
    ';
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
/* ----------- Recommendations functions */

function ReturnStyledCategoryName($categoryName)
{
    switch ($categoryName) {
            case 'Electricity (grid)':
                return "⚡ Electricity";
            case 'Gas usage':
                return "🔥 Gas";
            case 'Water usage':
                return "💧 Water";
            case 'Car travel':
                return "🚗 Car Travel";
            case 'Bus travel':
                return "🚌 Bus Travel";
            case 'Coach travel':
                return "🚐 Coach Travel";
            case 'Train travel (UK)':
                return "🚂 Train Travel";
        }
}

function DisplayRecommendations()
{
    $recommendationsArray = [];

    $db = dbConnect();

    // calculate total emissions per category, ordered by highest total emissions first
    $stmt = $db->prepare("
        SELECT C.Category_ID, C.Name, C.Emission_Unit, SUM(AL.Calculated_Emissions) AS Total
        FROM Activity_Log AL
        INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
        WHERE AL.User_ID = :user_id
        GROUP BY C.Category_ID
        ORDER BY Total DESC
    ");

    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $results = $stmt->execute();

    echo "<ul>";

    // loop through each category
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        
        $messageToShow = "No specific recommendation to give.";

        // retrieve the total carbon emissions for this category in the past 7 days
        $today = (new DateTime('now'))->format('Y-m-d');
        $weekAgo= (new DateTime('7 days ago'))->format('Y-m-d');

        $weekStmt = $db->prepare("
            SELECT SUM(Calculated_Emissions) AS Weekly_Total
            FROM Activity_Log
            WHERE User_ID = :user_id
            AND Category_ID = :category_id
            AND (
                substr(Activity_Date, 7, 4) || '-' || substr(Activity_Date, 4, 2) || '-' || substr(Activity_Date, 1, 2)
            ) BETWEEN :weekAgo AND :today
        ");
        $weekStmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
        $weekStmt->bindValue(':category_id', $row['Category_ID'], SQLITE3_TEXT);
        $weekStmt->bindValue(':weekAgo', $weekAgo, SQLITE3_TEXT);
        $weekStmt->bindValue(':today', $today, SQLITE3_TEXT);

        $weekResult = $weekStmt->execute();
        $weekRow = $weekResult->fetchArray(SQLITE3_ASSOC);

        $weeklyTotal = $weekRow['Weekly_Total'] ?? 0;

        
        // retrieve all messages and their trigger value for each category
        $recStmt = $db->prepare("
            SELECT Message, Weekly_Trigger_Value
            FROM Recommendations
            WHERE Category_ID = :category
            ORDER BY Weekly_Trigger_Value DESC");
        $recStmt->bindValue(':category', $row['Category_ID'], SQLITE3_TEXT);

        $recResult = $recStmt->execute();
        while ($recRow = $recResult->fetchArray(SQLITE3_ASSOC)) {
            $weeklyTrigger = $recRow['Weekly_Trigger_Value'];

            if ($weeklyTotal >= $weeklyTrigger) {
                $messageToShow = $recRow['Message'];
                break; 
            }
        }

        // display the corresponding recommendation and total emissions for each category, only if there has been an entry for it this week
        if ($weeklyTotal != 0) {
            $recommendationsArray[] = "
                <li style='margin-bottom: 20px; font-family: Glacial Indifference;'>
                    <h3 style='display:inline; font-size: 23px; font-weight: bold';'>" . ReturnStyledCategoryName($row['Name']) . " </h3><p style='font-size: 20px; font-style: italic; display:inline;'>- " . $weeklyTotal . " " . $row['Emission_Unit'] . " <b>this week</b></p>" . 
                    "<p style='font-size: 19px; margin-top: 5px;'>" . $messageToShow . "</p>" . 
                "</li>";
        }
    }

    // pass the php array to JS
    echo "<script>const recommendations = " . json_encode($recommendationsArray) . ";</script>";
    // do the same with the full list of recommendations for the random tips button
    $allRecommendationsArray = ReturnAllRecommendations();
    echo "<script>const allRecommendations = " . json_encode($allRecommendationsArray) . ";</script>";

    echo "<div id='recommendation-container' style='min-height: 100px;'>
            <p id='recommendation-text' style='padding: 0;'></p>
          </div>";

    echo "<button onclick='cycleRecommendation()' class='recommendation-button'> Next tip </button>";
    echo "<button onclick='randomRecommendations()' class='recommendation-button'> 5 random tips </button>";
    echo "</ul>";
}

function ReturnAllRecommendations()
{
    $db = dbConnect();
    $stmt = $db->prepare("
        SELECT R.*, E.Name
        FROM Recommendations R
        INNER JOIN Emission_Category E ON R.Category_ID = E.Category_ID
    ");
    $result = $stmt->execute();
    $allRecs = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $allRecs[] = "
                <li style='margin-bottom: 20px; font-family: Glacial Indifference;'>
                <h3 style='display:inline; font-size: 23px; font-weight: bold';'>" . ReturnStyledCategoryName($row['Name']) . " </h3>" . 
                "<p style='font-size: 19px; margin-top: 5px;'>" . $row['Message'] . "</p>" . 
                "</li>";
    }
    return $allRecs;
}

function DisplayTotalEmissions()
{
    $db = dbConnect();

    $stmt = $db->prepare("
            SELECT SUM(Calculated_Emissions) AS Grand_Total
            FROM Activity_Log AL
            WHERE AL.User_ID = :user_id 
        ");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $results = $stmt->execute();

    if ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $totalEmissions = $row['Grand_Total'] ?? 0;
        echo "<p style='font-size: 28px; margin-top: 5px; font-family: Glacial Indifference; color: #0c3b18; margin-left: 18px;'><b>" . $totalEmissions . "</b> kgCO₂e" . "</p>";
    }   
}

function DisplayDashboardActivityLog()
{
    if (!isset($_SESSION['user_id'])) {
        echo "";
        return;
    }

    // display log for this month
    DisplayTodaysActivities();
}

function DisplayTodaysActivities()
{
    $db = dbConnect();
    $today = (new DateTime('now'))->format('d-m-Y');


    // retrieve database values
    $stmt = $db->prepare("
            SELECT AL.*, C.Name, C.Unit, C.Emission_Unit
            FROM Activity_Log AL
            INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
            WHERE AL.User_ID = :user_id 
            AND AL.Activity_Date = :today
            ORDER BY AL.Activity_Date ASC
        ");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':today', $today, SQLITE3_TEXT);
    $results = $stmt->execute();

    // if no row is present, display message instead
    $firstRow = $results->fetchArray(SQLITE3_ASSOC);
    if (!$firstRow) {
        echo "<p style='text-align: center; font-style: italic; color: var(--main-theme-colour); padding: 20px;'>
                No activities logged for today yet!
              </p>";
        return;
    }

    echo "
            <table class='dashboard-activities-table'>
            <tr>
                <th>Category</th>
                <th>Value</th>
                <th>Notes</th>
                <th>CO2 Emissions</th>
            <tr></tr>
            </tr>";

    // display the first row
    echo "
        <tr id='row-{$firstRow['Log_ID']}'>
            <td>" . htmlspecialchars($firstRow['Name']) . "</td>
            <td>" . htmlspecialchars($firstRow['Value']) . " {$firstRow['Unit']}</td>
            <td>" . htmlspecialchars($firstRow['Notes']) . "</td>
            <td>{$firstRow['Calculated_Emissions']} {$firstRow['Emission_Unit']}</td>
        </tr>";
    
    // display table contents
    while ($row = $results->fetchArray(SQLITE3_ASSOC))
    {

        $logId = $row['Log_ID'];
        $escapedNotes = htmlspecialchars($row['Notes'], ENT_QUOTES);
        $escapedValue = htmlspecialchars($row['Value'], ENT_QUOTES);

            
        echo "
        <tr id='row-{$logId}'>
            <td>{$row['Name']}</td>
            <td>{$row['Value']} {$row['Unit']}</td>
            <td>{$row['Notes']}</td>
            <td>{$row['Calculated_Emissions']} {$row['Emission_Unit']}</td>
        </tr>";
            
    }
    echo "</table>";
    
}


/* ----------- Chart functions */

function ReturnTotalUserEmissions()
{
    $db = dbConnect();

    // retrieve database values
    $stmt = $db->prepare("
            SELECT SUM(AL.Calculated_Emissions) AS Total_Emissions, C.Name, C.Emission_Unit
            FROM Activity_Log AL
            INNER JOIN User U ON AL.User_ID = U.User_ID
            INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
            WHERE U.User_ID = :user_id 
            GROUP BY C.Name
            ORDER BY C.Name ASC
        ");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $results = $stmt->execute();

    if ($results) {
        $emissionsData = []; 
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $emissionsData[] = $row['Total_Emissions'];
        }

        return $emissionsData;
    }
    else {
        echo "No activities found or query failed.";
    }
}

function ReturnEmissionsCategories()
{
    $db = dbConnect();

    // retrieve database values
    $stmt = $db->prepare("
            SELECT C.Name, C.Emission_Unit
            FROM Activity_Log AL
            INNER JOIN User U ON AL.User_ID = U.User_ID
            INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
            WHERE U.User_ID = :user_id 
            GROUP BY C.Name
            ORDER BY C.Name ASC
        ");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $results = $stmt->execute();

    if ($results) {
        $emissionsData = []; 
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $emissionsData[] = $row['Name'];
        }

        return $emissionsData;
    }
    else {
        echo "No activities found or query failed.";
    }
}

function ReturnWeeklyEmissions()
{
    $db = dbConnect();

    $emissionsData = [];
    $today = (new DateTime('now'))->format('Y-m-d');
    $weekAgo= (new DateTime('6 days ago'))->format('Y-m-d');

    // retrieve database values
    $stmt = $db->prepare("
            SELECT SUM(AL.Calculated_Emissions) AS Total_Emissions, AL.Activity_Date
            FROM Activity_Log AL
            INNER JOIN User U ON AL.User_ID = U.User_ID
            INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
            WHERE U.User_ID = :user_id
            AND (
                substr(Activity_Date, 7, 4) || '-' || substr(Activity_Date, 4, 2) || '-' || substr(Activity_Date, 1, 2)
            ) BETWEEN :week_ago AND :today
            GROUP BY AL.Activity_Date
            ORDER BY AL.Activity_Date ASC
        ");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':week_ago', $weekAgo, SQLITE3_TEXT);
    $stmt->bindValue(':today', $today, SQLITE3_TEXT);
    $results = $stmt->execute();

    $weekArray = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $weekArray[$row['Activity_Date']] = $row['Total_Emissions'];
    }

    // fill in gaps from the select statement, defaulting to 0 emissions for days with no entries
    for ($i = 6; $i >= 0; $i--) {
        $date = (new DateTime("$i days ago"))->format('d-m-Y');
        $emissionsData[] = $weekArray[$date] ?? 0;
    }

    return $emissionsData;
}
function ReturnWeekDays()
{
    $today = (new DateTime('now'))->format('d-m-Y');

    $weekdays = [];
    for ($i = 6; $i >= 0; $i--) {
        $weekdays[] = (new DateTime("$i days ago"))->format('D');
    }

    return $weekdays;
}


function ReturnMonthlyEmissions()
{
    $db = dbConnect();

    $emissionsData = [];
    $today = (new DateTime('now'))->format('Y-m-d');
    $yearAgo= (new DateTime('first day of 11 months ago'))->format('Y-m-d');

    // retrieve database values
    $stmt = $db->prepare("
            SELECT SUM(AL.Calculated_Emissions) AS Total_Emissions, substr(AL.Activity_Date, 4, 7) AS Month_Year
            FROM Activity_Log AL
            INNER JOIN User U ON AL.User_ID = U.User_ID
            INNER JOIN Emission_Category C ON AL.Category_ID = C.Category_ID
            WHERE U.User_ID = :user_id
            AND (
                substr(Activity_Date, 7, 4) || '-' || substr(Activity_Date, 4, 2) || '-' || substr(Activity_Date, 1, 2)
            ) BETWEEN :year_ago AND :today
            GROUP BY Month_Year
        ");
    $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $stmt->bindValue(':year_ago', $yearAgo, SQLITE3_TEXT);
    $stmt->bindValue(':today', $today, SQLITE3_TEXT);
    $results = $stmt->execute();

    $monthArray = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $monthArray[$row['Month_Year']] = $row['Total_Emissions'];
    }

    // fill in gaps from the select statement, defaulting to 0 emissions for months with no entries
    for ($i = 11; $i >= 0; $i--) {
        $date = (new DateTime("first day of $i months ago"))->format('m-Y');
        $emissionsData[] = $monthArray[$date] ?? 0;
    }

    return $emissionsData;
}
function ReturnMonths()
{
    $today = (new DateTime('now'))->format('M');

    $months = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = (new DateTime("first day of $i months ago"))->format('M');
        $months[] = $date;
    }

    return $months;
}


/* ------------------ Activity log functions */

function DisplayActivityLog()
{
  if (!isset($_SESSION['user_id'])) {
      echo "";
      return;
  }
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
    $today = (new DateTime('now'))->format('Y-m-d');

    echo "
        <div id='addModal' class='activity-modal-overlay' style='display:none;'>
            <div class='modal-content add-modal'>
                <p style='text-align: center; font-family: \"Glacial Indifference Bold\"; font-size: 22px; margin: 0;'>Add Activity</p>
                <form method='POST'> <input type='hidden' name='action' value='add_activity'><br><br>
                    <label>Date: <input type='date' class='date-input' name='activity_date' value={$today}></label><br><br>
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

    }
    else {
        echo "No activities found or query failed.";
    }
}


/* ------------------ Activity log functions */

function DisplayAllUsers()
{
    $db = dbConnect();

    // retrieve database values
    $stmt = $db->prepare("
            SELECT U.*, SUM(AL.Calculated_Emissions) AS Total_Emissions
            FROM User U
            LEFT JOIN Activity_Log AL ON U.User_ID = AL.User_ID
            GROUP BY U.User_ID
            ORDER BY U.User_ID ASC
        ");
    $results = $stmt->execute();

    if ($results) {
        echo "
                <table class='activities-table'>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Total Emissions</th>
                    <th>Actions</th>
                </tr>";
    
        // display table contents
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            $total = $row['Total_Emissions'] ?: 0;

            $userID = $row['User_ID'];
            $escapedUsername = htmlspecialchars($row['Username'], ENT_QUOTES);
            $escapedEmail = htmlspecialchars($row['Email'], ENT_QUOTES);
            $escapedFname = htmlspecialchars($row['Fname'], ENT_QUOTES);
            $escapedLname = htmlspecialchars($row['Lname'], ENT_QUOTES);

            echo "
                <tr id='row-{$userID}'>
                    <td>{$userID}</td>
                    <td>{$row['Username']}</td>
                    <td>{$row['Email']}</td>
                    <td>{$row['Fname']}</td>
                    <td>{$row['Lname']}</td>
                    <td><b>{$total} kgCO₂e</b></td>
                    <td class='action-buttons'>
                        <button onclick='openEditUserModal({$userID}, \"{$escapedUsername}\", \"{$escapedEmail}\", \"{$escapedFname}\", \"{$escapedLname}\")' class='edit-delete-button'>Edit</button>
                        <button onclick='confirmDeleteUser({$userID})' class='edit-delete-button'>Delete</button>
                    </td>
                </tr>";
        }
        echo "</table>";

        // edit user modal
        echo "
            <div id='editModal' style='display:none;' class='activity-modal-overlay'>
                <div class='modal-content edit-modal'>
                    <p style='text-align: center; font-family: \"Glacial Indifference Bold\"; font-size: 22px; margin: 0;'>Edit User</p>
                    <form method='POST' style='margin-top: -60px'>
                        <input type='hidden' name='action' value='edit_user'><br><br>
                        <input type='hidden' name='user_id' id='edit_user_id'><br><br>
                        <label>Username: <input type='text' name='user_username' id='edit_username'></label><br><br>
                        <label>Email: <input type='email' name='user_email' id='edit_email'></label><br><br>
                        <label>First name: <input type='text' name='user_fname' id='edit_fname'></label><br><br>
                        <label>Last name: <input type='text' name='user_lname' id='edit_lname'></label><br><br>
                        <div class='button-group'>
                            <button type='submit' class='modal-button'>Save</button>
                            <button type='button' onclick='closeModal(\"editModal\")' class='modal-button'>Cancel</button>
                        </div>
                    </form>
                </div>
            </div>";

        // delete user modal
        echo "
            <div id='deleteModal' style='display:none;' class='activity-modal-overlay'>
                <div class='modal-content delete-modal'>
                    <p>Are you sure you want to delete this user?</p>
                    <form method='POST'>
                        <input type='hidden' name='action' value='delete_user'>
                        <input type='hidden' name='user_id' id='delete_user_id'>
                        <div class='button-group'>
                            <button type='submit' class='modal-button'>Yes, delete</button>
                            <button type='button' onclick='closeModal(\"deleteModal\")' class='modal-button'>Cancel</button>
                        </div>
                    </form>
                </div>
            </div>";

    }
    else {
        echo "No users found or query failed.";
    }
}


?>