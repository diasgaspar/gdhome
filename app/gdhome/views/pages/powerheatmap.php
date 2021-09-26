        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="resources/jquery/jquery-1.8.2.js"></script>  
        <script type="text/javascript" src="http://d3js.org/d3.v3.js"></script>
        <link rel="stylesheet" href="css/heatmap_style.css"/>
    <body>
        <div class="days-hours-heatmap">
            <!-- calibration and render type controller -->
            <div class="calibration" role="calibration">
                <div class="group" role="example">
                    <svg width="120" height="17">
                    </svg>
                    <div role="description" class="description">
                        <label>Less</label>
                        <label>More</label>
                    </div>        
                </div>
                <div role="toggleDisplay" class="display-control">
                    <div>
                        <input type="radio" name="displayType" checked/>
                        <label>count</label>
                    </div>
                    <div>
                        <input type="radio" name="displayType"/>
                        <label>daily</label> 
                    </div>
                </div>
            </div>
            <!-- heatmap -->
            <svg role="heatmap" class="heatmap"></svg>
        </div>

        <script type="text/javascript" src="app/gdhome/views/pages/powerhm_app.js"></script>
         <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    </body>
    
