<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>
  <?php if ($user): ?>

    <!-- body -->
    <div class="welcome-container">
        <h1>Dashboard</h1>
        <p>Your CO2 stats</p>
    </div>


    <div class="dashboard-container">

        <!-- activity log -->
        <div class="dashboard-activity-log">
            <h2 class="dashboard-title">Today's Activity</h2>
            <?php
            DisplayDashboardActivityLog();
            echo "<br><a href='activity-log.php' style='font-family: Glacial Indifference Bold; font-size: 20px; color: var(--main-theme-colour); text-decoration: none; margin-left: 35px;'>Go to Activity Log</a>";
            ?>
        </div>


        <!-- recommendations -->
        <div class="recommendations-container">
            <p style='font-family: Glacial Indifference Bold; font-size: 30px;'>Emission tips!</h3>
            <?php
            DisplayRecommendations()
            ?>
        </div>
        

        <!-- bar chart -->
        <div class="chart-container">
            <h2 class="dashboard-title">This Week's Emissions</h2>
            <canvas id="barWeeklyEmissions"></canvas>
        </div>


        <!-- line graph -->
        <div class="chart-container">
            <h2 class="dashboard-title">This Year's Emissions</h2>
            <canvas id="lineMonthlyEmissions"></canvas>
        </div>


        <!-- pie chart -->
        <div class="chart-container">
            <h2 class="dashboard-title">Total Emissions by Category</h2>
            <canvas id="chartCategories"></canvas>
        </div>


        <!-- total emissions -->
        <div class="total-emissions-container">
            <h2 class="dashboard-title">Total Emissions</h2>
            <?php
            DisplayTotalEmissions()
            ?>
        </div>

        <!-- total monthly insights wrapper -->

        <div class="monthly-insights-wrapper">
        <?php
        DisplayMonthlyCarbonInsights();
        ?>
    </div>

    <!-- carbon goal tracker -->
    <div class="carbon-goal-wrapper">
        <?php
        DisplayCarbonGoalTracker();
        ?>
    </div>
        

    </div>


    <?php
    $userEmissions = ReturnTotalUserEmissions(); 
    $emissionsCategories = ReturnEmissionsCategories(); 

    echo "<script>";
    echo "const userEmissions = " . json_encode($userEmissions) . ";";
    echo "const emissionsCategories = " . json_encode($emissionsCategories) . ";";
    echo "</script>";
    ?>

    <script> // Pie chart - rendering
        const categoryNames = ['Electricity','Gas','Water','Car Travel', 'Bus Travel','Coach travel'];
        const emissions = [ 10.8042, 15.0439, 17.3501, 7.15, 1.5, 0 ];

        const x = document.getElementById('chartCategories')
        const categoryPieChart = new Chart(x,{
            type: 'pie',
            data: {
            labels: emissionsCategories,
            datasets: [{
                label: 'CO2 emission',
                data: userEmissions,
                backgroundColor: [
                'rgba(14, 49, 0, 0.8)',
                'rgba(54, 105, 34, 0.8)',
                'rgba(73, 162, 39, 0.8)',
                'rgba(158, 200, 141, 0.8)',
                'rgba(178, 226, 125, 0.8)',
                'rgba(101, 209, 121, 0.8)',
                ],
                borderColor: [
                'rgba(0, 0, 0, 0.0)',
                'rgba(0, 0, 0, 0.0)',
                'rgba(0, 0, 0, 0.0)',
                'rgba(0, 0, 0, 0.0)',
                'rgba(0, 0, 0, 0.0)',
                'rgba(0, 0, 0, 0.0)',
                ],

                borderWidth: 1
            }]
            },
            options: {
            scales: {
                y:{
                beginAtZero:true,
                ticks: {
                    display: false
                },
                grid: {
                    display:false
                }
                }
            }
            }
        });
    </script>


    <?php
    $weeklyEmissions = ReturnWeeklyEmissions(); 
    $weekDays = ReturnWeekDays(); 

    echo "<script>";
    echo "const weeklyEmissions = " . json_encode($weeklyEmissions) . ";";
    echo "const weekDays = " . json_encode($weekDays) . ";";
    echo "</script>";
    ?>

    <script> // bar chart - rendering
        const y = document.getElementById('barWeeklyEmissions')
        const weeklyEmisionsBar = new Chart(y,{
          type: 'bar',
          data: {
            labels: weekDays,
            datasets: [{
              data: weeklyEmissions,
              backgroundColor: [
                'rgba(40, 115, 28, 0.9)',
              ],
              borderColour: [
                'rgba(0, 0, 0, 0.2)',
              ],

              borderWidth: 1
            }]
          },
          options: {
            plugins:{
              legend:{
                display: false
              }
            },
            scales: {
              y:{
                beginAtZero:true,
              }
            }
          }
        });
    </script>



    <?php
    $monthlyEmissions = ReturnMonthlyEmissions(); 
    $months = ReturnMonths(); 

    echo "<script>";
    echo "const monthlyEmissions = " . json_encode($monthlyEmissions) . ";";
    echo "const months = " . json_encode($months) . ";";
    echo "</script>";
    ?>

    <script> // line graph- rendering
        document.addEventListener("DOMContentLoaded", function() {

        const y = document.getElementById('lineMonthlyEmissions');
        
        new Chart(y, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Monthly Emissions',
                    data: monthlyEmissions,
                    backgroundColor: 'rgba(40, 115, 28, 0.2)',
                    borderColor: 'rgba(40, 115, 28, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    });

    </script>

    <?php endif; ?>
    <?php
      include("components/loginprompt.php");
      include("components/footer.php");
    ?>
