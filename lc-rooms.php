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
$alloncampus = array();
$today = date('Y-m-d');
foreach($classes as $class) {
	if($class[8] >= $today) {
		if($class[23] == 188 || $class[23] == 186 || $class[23] == 239) {
			array_push($alloncampus,$class);
		}
	}
}
echo json_encode($alloncampus);
