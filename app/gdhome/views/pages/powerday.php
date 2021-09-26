 <!-- Report specific-->  
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="jquery-1.8.2.js"></script>  

   <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart","table"]});
      google.setOnLoadCallback(drawChart);
      
    function drawChart() {       
       var jsonData = $.ajax({
          url: "app/gdhome/views/pages/powerday_getdata.php",
          dataType:"json",
          async: false
          }).responseText;

       
        var options = {
           title: 'Consumo diário (Wh)',
          hAxis: {title: 'dia', titleTextStyle: {color: 'red'}, maxAlternation:30, fontSize:6 },
                isStacked: true
        };
        var data = new google.visualization.DataTable(jsonData);
             
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
      
    
    </script>

    
    <body>

    

<div class="row">
<h2 class="sub-header">Power Daily</h2>

        <div class="col-md-4 col-md-offset-1">
            <div class="panel-body">
                <div class="well">
                    
                    <table class="table table-condensed">
                        <tr>
                            <td colspan="3">from <?php print $monthAgoChargingStartDate->format("Y-m-d"); ?> to <?php print $monthAgoChargingEndDate->format("Y-m-d"); ?></td>
                        </tr>
                        <tr>
                            <td><p align="left">
                                    <small>T1(vazio)<br> </small>
                                    <small>T2+T3(fora)<br></small>
                                    <small>Total</small><br>
                                </p>
                            </td>
                            <td><p align="left">
                                    <small><?php print $energySpentMonthAgo_V_Kwh ?>KWh * <?php print gdhome\HomeVars::ENERGY_COST_T1 ?>€ = <?php print $costFromMonthAgo_V ?>€<br> </small>
                                    <small><?php print $energySpentMonthAgo_F_Kwh ?>KWh * <?php print gdhome\HomeVars::ENERGY_COST_T2T3 ?>€ = <?php print $costFromMonthAgo_F ?>€<br></small>
                                    <small><?php print $energySpentMonthAgo_Total_Kwh ?>Kwh<br></small>
                                </p>
                            </td>
                            <td>
                                <br> 
                                <?php print $costFromMonthAgo_Total ?>€<br>
                            </td>
                        </tr>

                        <tr>
                            <td><p align="left"><small>Termo de Pot</small></p></td>
                            <td><p align="left"><small><?php print $numDays ?>days * <?php print gdhome\HomeVars::ENERGY_COST_POT ?>€</small></p></td>
                            <td><?php print $costPowerContract ?>€</td>
                        </tr>

                        <tr>
                            <td><p align="left"><small>Contr. Audiovisual</small></p></td>
                            <td><p align="left"><small>1month * <?php print gdhome\HomeVars::ENERGY_COST_CONTR_AUDIOVISUAL ?>€</small></p></td>
                            <td><?php print gdhome\HomeVars::ENERGY_COST_CONTR_AUDIOVISUAL ?>€</td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><p align="right">Total</p></td>
                            <td><b><?php print $totalCost; ?>€</b></td>
                        </tr>

                    </table>
                </div>     
            </div>
        </div>
    
    <div class="col-md-4 col-md-offset-1">
        <div class="panel-body">
            <div class="well">
                <table class="table table-condensed">
                    <tr>
                        <td colspan="3">from <?php print $previousDateAtSpecificDayCalculation->format("Y-m-d"); ?> until yesterday</td>
                    </tr>
                    <tr>
                        <td><p align="left">
                                <small>T1(vazio)<br> </small>
                                <small>T2+T3(fora)<br></small>
                                <small>Total</small><br>
                            </p>
                        </td>
                        <td><p align="left">
                                <small><?php print $energySpentFromPreviousChargingDay_V_Kwh ?>KWh * <?php print gdhome\HomeVars::ENERGY_COST_T1 ?>€ = <?php print $costFromPreviousChargingDay_V ?>€<br> </small>
                                <small><?php print $energySpentFromPreviousChargingDay_F_Kwh ?>KWh * <?php print gdhome\HomeVars::ENERGY_COST_T2T3 ?>€ = <?php print $costFromPreviousChargingDay_F ?>€<br></small>
                                <small><?php print $energySpentFromPreviousChargingDay_Total_Kwh ?>Kwh<br></small>
                            </p>
                        </td>
                        <td>
                            <br> 
                            <?php print $costFromPreviousChargingDay_Total ?>€<br>
                        </td>
                    </tr>

                    <tr>
                        <td><p align="left"><small>Termo de Pot</small></p></td>
                        <td><p align="left"><small><?php print $numDaysFromPreviousChargingDay ?>days * <?php print gdhome\HomeVars::ENERGY_COST_POT ?>€</small></p></td>
                        <td><?php print $costPowerContractFromPreviousChargingDay ?>€</td>
                    </tr>

                    <tr>
                        <td><p align="left"><small>Contr. Audiovisual</small></p></td>
                        <td><p align="left"><small>1month * <?php print gdhome\HomeVars::ENERGY_COST_CONTR_AUDIOVISUAL ?>€</small></p></td>
                        <td><?php print gdhome\HomeVars::ENERGY_COST_CONTR_AUDIOVISUAL ?>€</td>
                    </tr>

                    <tr>
                        <td></td>
                        <td><p align="right">Total</p></td>
                        <td><b><?php print $totalCostFromPreviousChargingDay; ?>€</b></td>
                    </tr>

                </table>
            </div>

        </div>

    </div>
</div>


<div class="panel panel-default">
  <div class="panel-body">
    <div id="chart_div"></div>
  </div>
    <?php    echo "hello this is powerday";?>
</div>


 

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    
  </body>


