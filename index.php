<?php
use gdhome\Routes as Routes;
require_once 'app/start.php';
error_reporting( E_ALL | E_STRICT );
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    
    
    <link rel="icon" href="../../favicon.ico">

    <title>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="resources/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/navbar.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<?php
    if (isset($_GET['controller']) && isset($_GET['action'])) {
      $controller = $_GET['controller'];
      $action     = $_GET['action'];
    } else {
      $controller = 'pages';
      $action     = 'home';
    }  
?>
  </head>

  <body>

    <div class="container">

      <!-- Static navbar -->
      <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="?controller=pages&action=home">gdhome</a>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="?controller=pages&action=home">Home</a></li>
              <li><a href="#">About</a></li>
              <li><a href="#">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Power <span class="caret"></span></a>
                <ul class="dropdown-menu">
                   <li class="<?php if ($action == "home") echo 'active'; ?>"><a href="?controller=pages&action=home">Overview <span class="sr-only">(current)</span></a></li>
                    <li class="<?php if ($action == "powerday") echo 'active'; ?>"><a href="?controller=pages&action=powerday">Power Day</a></li>
                    <li class="<?php if ($action == "powermonth") echo 'active'; ?>"><a href="?controller=pages&action=powermonth">Power Month</a></li>
                    <li class="<?php if ($action == "powerheatmap") echo 'active'; ?>"><a href="?controller=pages&action=powerheatmap">Daily Power Heatmap</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Temp <span class="caret"></span></a>
                <ul class="dropdown-menu">
                   <li class="<?php if ($action == "home") echo 'active'; ?>"><a href="?controller=pages&action=home">Overview <span class="sr-only">(current)</span></a></li>
                    <li class="<?php if ($action == "powerday") echo 'active'; ?>"><a href="?controller=pages&action=powerday">Power Day</a></li>
                    <li class="<?php if ($action == "powermonth") echo 'active'; ?>"><a href="?controller=pages&action=powermonth">Power Month</a></li>
                    <li class="<?php if ($action == "powerheatmap") echo 'active'; ?>"><a href="?controller=pages&action=powerheatmap">Daily Power Heatmap</a></li>
                  <li role="separator" class="divider"></li>
                  <li class="dropdown-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
              <li class="active"><a href="?controller=pages&action=powerday">Default <span class="sr-only">(current)</span></a></li>
              <li><a href="../navbar-static-top/">Static top</a></li>
              <li><a href="../navbar-fixed-top/">Fixed top</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>

      <!-- Main component for a primary marketing message or call to action -->
    
        
            
  

    </div> <!-- /container -->

    
    <div class="row placeholders">
            
              <?php

              $controllers = array('pages' => ['home', 'error', 'powerday', 'powermonth','powerheatmap','chartRangeFilter'],
                       'posts' => ['index', 'show']);
              $routes = new Routes();
              
              if (array_key_exists($controller, $controllers)) {
                    if (in_array($action, $controllers[$controller])) {
                      $routes->call($controller, $action);
                    } else {
                      $routes->call('pages', 'error');
                    }
              } 
              else {
                    $routes->call('pages', 'error');
              }
              
              ?>
              
          </div>

                
    <!-- Bootstrap core JavaScript
    ================================================== -->
    
    <script src="resources/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  </body>
</html>
