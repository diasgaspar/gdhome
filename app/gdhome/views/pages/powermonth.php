 <!-- Report specific-->  
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="jquery-1.8.2.js"></script>  

   <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart","table"]});
      google.setOnLoadCallback(drawChart);
      
    function drawChart() {       
       var jsonData = $.ajax({
          url: "app/gdhome/views/pages/powermonth_getdata.php",
          dataType:"json",
          async: false
          }).responseText;

       
        var options = {
           title: 'Monthly Consumption (KWh)',
          hAxis: {title: 'year-month', titleTextStyle: {color: 'red'}, maxAlternation:30, fontSize:6 },
                isStacked: true
        };
        var data = new google.visualization.DataTable(jsonData);
             
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
      
    
    </script>

    
    <body>

    <h2 class="sub-header">Power Monthly</h2>



<div class="panel panel-default">
  <div class="panel-body">
    <div id="chart_div"></div>
  </div>
</div>


 

    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    
  </body>


