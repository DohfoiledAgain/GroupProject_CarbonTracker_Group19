<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>
<body>
    <!-- navbar -->
    <?php
    include("components/navbar.php");
    ?>

    <!-- body -->
    <div class="welcome-container">
        <h1 style="font-size: 50px; margin-bottom: -15px;">Activity Log</h1>
        <p style="font-size: 30px;">Log your activities.</p>
    </div>
    
    <div class="activity-log-container">
        <table class="activities-table">
            <tr>
                <th>Activity</th>
                <th>Water</th>
                <th>Transport</th>
                <th>Gas</th>
                <th>Electricity</th>
            </tr>
            <tr>
                <td>Bus</td>
                <td>0 m^3</td>
                <td>0.5 Tmiles</td>
                <td>0 kwh</td>
                <td>3 kwh</td>
            </tr>
            <tr>
                <td>Avacados</td>
                <td>5 m^3</td>
                <td>40 Tmiles</td>
                <td>0 kwh</td>
                <td>0 kwh</td>
            </tr>
        </table>
    </div>
    
    <script src="functions.js"></script>
</body>
</html>