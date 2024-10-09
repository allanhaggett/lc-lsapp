<?php 
opcache_reset();
$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\inc\lsapp.php';
require($path);
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>Testing</title>
  </head>
  <body>

  <nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
              <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
        </ul>
    </div>
  </nav>
    
  <main role="main">
    <div class="container">
      
      <?php 
      # mktime(hour, minute, second, month, day, year)
      $next_month = mktime(0, 0, 0, 4, 0, 2023);
      echo $next_month;
      $date_var = date("t", $next_month);
      echo "\n" . $date_var;
      ?>


      <div class="row justify-content-md-center mt-5">
        <div class="card">

            <div class="col-md-8">
              SBCPS: Summative Evaluation Overall
            </div>
         
          
            <div class="col-md-4">
              <?php if(canAccess()): ?>
                <button type="button" 
                  class="btn btn-info" 
                  data-toggle="modal" 
                  data-target="#modalYT">
                  View Report
                </button>
            </div>
        </div>
          
        
        

    <!--Modal: Name-->
    <div class="modal fade" id="modalYT" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">

    <!--Content-->
    <div class="modal-content">

        <!--Body-->
        
        
        
        <div class="modal-body mb-0 p-0">

        <div class="embed-responsive embed-responsive-16by9 z-depth-1-half">
                <iframe src="https://reporting.alchemer-ca.com/r/50010814_5fb6d965f0e431.70423871" 
                    frameborder="0" 
                    width="1920" 
                    height="100%" 
                    class="overflow-hide">
                </iframe>
        </div>

         


        </div>

        <!--Footer-->
        <div class="modal-footer justify-content-center">
        <span class="mr-4">Footer Details Here</span>
        
        <button type="button" class="btn btn-outline-primary btn-rounded btn-md ml-4" data-dismiss="modal">Close</button>

        </div>

    </div>
    <!--/.Content-->

    </div>
    </div>
    <!--Modal: Name-->




<div class="modal fade" id="modalGM" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">

    <a data-toggle="modal" href="https://reporting.alchemer-ca.com/r/50010814_5fb6d965f0e431.70423871" data-target="#modal">Click me</a>

</div>
</div>

    <?php else: ?>
        No access for you
    <?php endif ?>
    </div>
    </div>
    </div>
    
    </main>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>


  </body>
</html>