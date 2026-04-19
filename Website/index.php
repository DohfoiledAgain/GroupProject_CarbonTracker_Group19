<!DOCTYPE html>
<html lang="en">
  <?php
  include("components/head.php");
  ?>

    <!-- body -->
    <div class="welcome-container">
        <h1>Dashboard</h1>
        <p>Your CO2 stats</p>
    </div>
    
    <br><br><br>
    <div class="dashboard-container"  style="width: 600px; height: 600px;">
        <!-- graphs display pie chart -->
        <h2>Emissions by Category</h2>
        <canvas id="chartCategories"></canvas>
        <br><h2>Weekly Emissions</h2>
        <canvas id="barWeeklyEmissions"></canvas>
    </div>

    <div class="dashboard-container"  style="width: 600px; height: 600px;">
        <!-- graphs display bar chart -->
        <canvas id="barWeeklyEmissions"></canvas>
    </div>


    <script> // Pie chart - rendering
    const categoryNames = ['Electricity','Gas','Water','Car Travel', 'Bus Travel','Coach travel'];
    const emissions = [ 10.8042, 15.0439, 17.3501, 7.15, 1.5, 0 ];

    const x = document.getElementById('chartCategories')
    const categoryPieChart = new Chart(x,{
      type: 'pie',
      data: {
        labels: categoryNames,
        datasets: [{
          label: 'CO2 emission',
          data: emissions,
          backgroundColor: [
            'rgba(214, 226, 45, 1)',
            'rgba(35, 219, 173, 1)',
            'rgba(20, 3, 253, 1)',
            'rgba(167, 55, 55, 1)',
            'rgba(181, 72, 190, 1)',
            'rgba(0, 0, 0, 1)',
          ],
          borderColour: [
            'rgba(214, 226, 45, 1)',
            'rgba(35, 219, 173, 1)',
            'rgba(20, 3, 253, 1)',
            'rgba(167, 55, 55, 1)',
            'rgba(181, 72, 190, 1)',
            'rgba(0, 0, 0, 1)',
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

    <script> // bar chart - rendering
    const Weekdays = ['Mon','Tues','Wed', 'Thur', 'Fri','Sat', 'Sun'];
    const weeklyEmissions = [ 16.8042, 13.0439, 9.3501, 10.15, 6.203, 8.345, 7.5607 ];

    const y = document.getElementById('barWeeklyEmissions')
    const weeklyEmisionsBar = new Chart(y,{
      type: 'bar',
      data: {
        labels: Weekdays,
        datasets: [{
          label: Weekdays,
          data: weeklyEmissions,
          backgroundColor: [
            'rgba(214, 226, 45, 1)',
            'rgba(35, 219, 173, 1)',
            'rgba(20, 3, 253, 1)',
            'rgba(167, 55, 55, 1)',
            'rgba(181, 72, 190, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(72, 13, 167, 1)',
          ],
          borderColour: [
            'rgba(214, 226, 45, 1)',
            'rgba(35, 219, 173, 1)',
            'rgba(20, 3, 253, 1)',
            'rgba(167, 55, 55, 1)',
            'rgba(181, 72, 190, 1)',
            'rgba(0, 0, 0, 1)',
            'rgba(72, 13, 167, 1)'
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

    <div id="Reccomendations"> <!-- emission reducing tips button-->
      <button onclick="RandomReccomendation()" style="font-size: 25px; background-color: alice; color: grey; border-radius: 25px;"> Reduction Reccomendation</button>
      <br><br>
      <p id="recbutt" style="color: #73AD21;">Press to get emission reduction tips</p>
    </div>
    <script>
      
      function RandomReccomendation() //function - picks randomly between tips for reducing emissions
      {  
        const reccs = ["Electricity Usage - Remember to turn off your lights when a room isn't in use!", "Gas Usage - Keep your doors and windows closed when the heating is on!", "Water Usage - Turn off the tap while you brush your teeth!", "Car Travel - Try to combine your errands into one single loop trip!", "Bus Travel - Walk or cycle for any trips that are under one mile!", "Coach/Train Travel - Try to book direct routes to avoid the unnecessary miles!"];
        const random = Math.floor(Math.random() * reccs.length);
        document.getElementById("recbutt").innerHTML = (random, reccs[random]);
      }
    </script>

    <?php
      include("components/loginprompt.php");
    ?>
    <script src="js/functions.js"></script>

  

</body>
</html>