<?php 
opcache_reset();
# $path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\inc\lsapp.php';
# require($path);
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

      
      <?php 
      date_default_timezone_set('UTC');
      # mktime(hour, minute, second, month, day, year)
      $today = date("m");
      $today += 1;
      echo $today;
      $next_month = mktime(0, 0, 0, $today, 0, 0);
      echo date("M", $next_month);
      $date_var = date("t", $next_month);
      echo " has $date_var days";

      echo "<br>";

      # for each month's number of days
        foreach(range(1, 12) as $months) {
            $month_unix = mktime(0, 0, 0, $months, 14, 2023);
            $month = date("M", $month_unix);
            $days = date("t", $month_unix);
            echo "<br>$month has $days days";
        }
        echo "<br>";
        # for each day
          # Do the thing
        foreach(range(1, 12) as $months) {
            echo $months . "<br>";
            $month_unix = mktime(0, 0, 0, $months, 0, 2023);
            echo $month_unix . "<br>"; # 1672473600, 1672444800
            $month = date("M", $month_unix);
            echo "$month, <br>";
            // returns Dec, Jan, Feb, Mar, Apr, May, Jun, Jul, Aug, Sep, Oct, Nov, 
        }
      ?>


    


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>


  </body>
</html>