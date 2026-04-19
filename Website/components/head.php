
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Footprint Tracker</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="css/termsconditions.css" />
    <link rel="stylesheet" href="css/loginoverlay.css" />
    <script src="js/functions.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <?php
      require_once("components/functions.php");
      $user = getUser();
    ?>
</head>
<body>
    <!-- navbar -->
    <?php
      include("components/navbar.php");
    ?>