<html lang=en>

<head>

  <title></title>

</head>

<body>

<?php
opcache_reset();

$test_city = array('delta', 'Victoria', 'Smithers');

echo "Hello World" . "</br>";

// Opens the cities.csv file as a giant 0-80 array
$cities = file ("cities.csv");

// Goes through each line as the variable $city.  It appears this turns info into a string.
foreach ($cities as $city) {

  // Used explode to separate the line back into an array.
  $city_array = explode (",", $city); 
    
  foreach ($test_city as $test_city_array) {
    // Compares the city we're checking against the city in each row of the cities list
    // (case insensitive) and when it matches, outputs the corresponding region.
    if (strcasecmp($test_city_array, $city_array[1]) == 0) {
      echo $test_city_array . " is part of " . $city_array[2];
      echo '</br>';
    }



  }
   
}


?>


</body>



</html>
