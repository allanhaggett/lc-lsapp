<?php
opcache_reset();

// The goal of this page is to use the GBC_ROSTER_EXPANDED to export a
// class list, and use the Home City field to compare against a created
// list of cities with the region assigned - the end goal being to be
// able to output the number of participants from each region.

// GBC_ROSTER_EXPANDED.csv columns for reference:
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Course [0], Class Code [1], Start Date [2], First Name [3], Last Name [4],
// EmpID [5], Job Title [6], Position [7], Ministry [8], Current Status [9],
// Previous Status [10], Waitlist Number [11], Enrolled Date [12],
// Approved On [13]], Approved By [14], Completion Date [15], Drop Date [16],
// Date Learner Added [17], Learner Added By [18], Enrollment Last Modified [19],
// Enrollment Last Modified By [20], Learner Email [21], Learner IDIR [22],
// Home City [23], Learner ID [24]
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


// Setup variables




// Regions
// Establish each region as an integer variable to apply a counter
$greatervictoria = 0;
$lowermainland = 0;
$southvi = 0;
$northvi = 0;
$southbc = 0;
$centralbc = 0;
$northbc = 0;


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Open the cities.csv file, create an array, and assign row values to variables
//$fcities = fopen('cities.csv', 'r');

// fgetcsv($fcities);

// $cityid = $fcities[0]
// $cityname = $fcities[1]
// $cityregion = $fcities[2]


// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// I want to loop through each row of GBC_ROSTER_EXPANDED.csv, checking
// $rostercity against each row of cities.csv as $cityname. when the two
// values == grab the corresponding $cityregion and add 1 to that region's
// variable (eg. $greatervictoria++)

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
// Open the GBC_ROSTER_EXPANDED.csv file, create an array, and assign row
// values to variables.

// I only want to use the Class Code and Home City fields, so not sure if I
// need to define the remaining columns below
//$froster = fopen('GBC_ROSTER_EXPANDED.csv', 'r');

//fgetcsv($froster);

//foreach ($froster as $rosterline) {
  //$itemcode = $rosterline[1];
  //$rostercity = $rosterline[23];

    // calling in the cities.csv file, and defining each column
    // Not sure this is the best place to do this
//    fgetcsv($fcities);
//    $cityid = $fcities[0]
//    $cityname = $fcities[1]
//    $cityregion = $fcities[2]

//    if ($rostercity == $cityname) {
      // if $rostercity is equal to $cityname grab the $cityregion of the same row
//        $cityregion = $region_tally;

      // Once I have the region, I need a way to match to the corresponding region
      // variable above, and add one to the counter
//        if ($region_tally == 'Greater Victoria') {
//          $greatervictoria++;
//        } elseif ($region_tally == 'Lower Mainland') {
//          $lowermainland++;
//        } elseif ($region_tally == 'Southern Vancouver Island') {
//          $southvi++;
//        } elseif ($region_tally == 'Northern Vancouver Island') {
//          $northvi++;
//      } elseif ($region_tally == 'Southern BC') {
//          $southbc++;
//        } elseif ($region_tally == 'Central BC') {
//          $centralbc++;
//        } elseif ($region_tally == 'Northern BC') {
//          $northbc++;
//        }

    }


//    else {
//      continue;
      // The intent here is for if the cities don't match, it will continue to the
      // next row of $fcities to see if that one matches, etc.
//    }














//fclose($fcities);
//fclose($froster);


?>
