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