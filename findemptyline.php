<?php
// Let's get started
require('inc/lsapp.php');
// Get the full class list
$c = getClasses();

// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the start date to
// the temp array
$count = 0;
foreach($c as $line) {
    $count++;
	// if(empty($line[8])) echo $count . '<br>';
    echo $line[0] . ' - ' . $line[8] . ' Exists.<br>';
}