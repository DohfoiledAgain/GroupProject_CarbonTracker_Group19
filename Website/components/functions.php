<?php
  session_start();

  /* ------------------ Database functions */

  function dbConnect(){
    //$db = new SQLite3("database.db");
    return $db;
  }

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

?>