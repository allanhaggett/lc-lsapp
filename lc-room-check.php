<?php 
header('Content-Type: application/json');
require('inc/lsapp.php');
$date = (isset($_GET['date'])) ? $_GET['date'] : 0;
// Get the full class list
$classes = getClasses();
// Grab the headers
// $headers = $courses[0];
// Pop the headers off the top
array_shift($classes);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the start date to
// the temp array
foreach($classes as $line) {
	$tmp[] = $line[8];
}
// Sort the whole kit and kaboodle by name
array_multisort($tmp, SORT_ASC, $classes);
$onthatday = array();
foreach($classes as $class) {
		if(($date >= $class[8]) && ($date <= $class[9])) {
			if($class[23] == 188 || $class[23] == 186 || $class[23] == 239){
				array_push($onthatday,$class);
			}
		}
}

echo json_encode($onthatday);
