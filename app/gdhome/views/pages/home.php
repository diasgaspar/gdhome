

    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!--<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>-->
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="jquery-1.8.2.js"></script> 
    <script type="text/javascript">
    
    // Load the Visualization API and the piechart package.
    google.charts.load('current', {'packages':['corechart']});
      
    // Set a callback to run when the Google Visualization API is loaded.
    google.charts.setOnLoadCallback(drawChart);
      
    function drawChart() {
          
      
      var jsonData2 = $.ajax({
          url: "app/gdhome/views/pages/home_getdata.php",
          dataType: "json",
          async: false
          }).responseText;
       
      var options3 = {
           title: 'Potência',
                vAxis: {logScale:true} ,
                hAxis: {maxAlternation:30, fontSize:6 },
                colors: ['#867E7E', 'blue']
                
            }
      // Create our data table out of JSON data loaded from server.
      var data2 = new google.visualization.DataTable(jsonData2);

      // Instantiate and draw our chart, passing in some options.
      var chart2 = new google.visualization.LineChart(document.getElementById('chart2_div'));
      chart2.draw(data2, options3);
      
    }

    </script>







<div class="row">

  <div class="col-md-2 col-md-offset-1">
      <div class="panel-body">
          <div class="well">
            Temperature
            <h2><?php print $latestHomeMeas->getTemp()?>&deg;</h2>
          </div>     
      </div>
  </div>

  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
            Current
            <h2><?php print $latestHomeMeas->getRxI()/10?>A</h2>
        </div>     
      </div>
  </div>

  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
            Power
            <h2><?php print $latestHomeMeas->getRxPw()?>W</h2>
        </div>     
      </div>
  </div>

  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
            Energy spent today
            <h3><?php print $energySpentToday_Ewh; ?>Wh</h3>
        </div>     
      </div>
  </div>

  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
            Energy spent yesterday
            <h3><?php print $energySpentYesterday_Ewh; ?>Wh</h3>
        </div>     
      </div>
  </div>
    
</div>


      

<div class="panel panel-default">
  <div class="panel-body">
    <div id="chart2_div"></div>
  </div>
</div>


<div class="row">

 <div class="col-md-2 col-md-offset-1">
      <div class="panel-body">
        <div class="well">
          <p class="text-left">Today's Cost</p>
            <p class="text-right">
              <small>Energy only: <?php print $energyCostToday_cost?>€ </small><br>
              <small>Pw Cont: <?php print $energyCostToday_numDays."d * ".\gdhome\HomeVars::ENERGY_COST_POT ?> = <?php print $costToday_powercontract;?>€</small><br>
            </p>
           <p class="text-left"><h3>=<?php print $energyCostTodayWithPowerContract; ?>€</h3></p>
        </div>     
      </div>
  </div>


  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
          <p class="text-left">Yesterday's Cost</p>
            <p class="text-right">
              <small>Energy only: <?php print $costYesterday_cost?>€ </small><br>
              <small>Pw Cont: <?php print $costYesterday_numDays."d * ".\gdhome\HomeVars::ENERGY_COST_POT ?> = <?php print $costYesterday_powercontract?>€</small><br>
            </p>
           <p class="text-left"><h3>=<?php print $costYesterday_powercontract; ?>€</h3></p>
        </div>     
      </div>
  </div>
    
  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
            <p class="text-left">cost since <?php print $previousDateAtSpecificDayCalculation->format("Y-m-d")?></p>
            <p class="text-right">
              <small>Energy (<?php print $energySpentFromPreviousChargingDay_Kwh;?>KWh): <?php print $costFromPreviousChargingDay_cost?>€ </small><br>
              <small>Pw Cont: <?php print $costFromPreviousChargingDay['numDays']."d * ".\gdhome\HomeVars::ENERGY_COST_POT ?> = <?php print number_format($costFromPreviousChargingDay['numDays']*\gdhome\HomeVars::ENERGY_COST_POT,2)?>€</small><br>
            </p>
           <p class="text-left"><h3>=<?php print(number_format($costFromPreviousChargingDayWithPowerContract,2)); ?>€</h3></p>
        </div>     
      </div>
  </div>  
   
  <div class="col-md-2">
      <div class="panel-body">
        <div class="well">
            <p class="text-left">last charged cost:<br> 
            <small><?php print $monthAgoChargingStartDate->format("Y-m-d")?> to <?php print $monthAgoChargingEndDate->format("Y-m-d")?></small>
            
            </p>
            <p class="text-right">
              <small>Energy (<?php print number_format($energySpentMonthAgo['Ewh']/1000,0); ?>KWh): <?php print number_format($costFromMonthAgo['cost'],2)?>€ </small><br>
              <small>Pw Cont: <?php print $costFromMonthAgo['numDays']."d * ".\gdhome\HomeVars::ENERGY_COST_POT ?> = <?php print number_format($costFromMonthAgo['numDays']*\gdhome\HomeVars::ENERGY_COST_POT,2)?>€</small><br>
            </p>
           <p class="text-left"><h3>=<?php print(number_format($costFromMonthAgoWithPowerContract,2)); ?>€</h3></p>
        </div>     
      </div>
  </div>    

    
</div>



   <?php 
   echo "last record at ".$latestHomeMeas->getTimeStamp()."\n";
   echo $_SERVER['SERVER_NAME']."\n";
      
   ?>
   <!-- Bootstrap core JavaScript
    ================================================== -->
    
   <!-- <script src="resources/bootstrap/js/bootstrap.min.js"></script>-->
   <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="jquery-1.8.2.js"></script> 

    
    

