<?php 


require('inc/lsapp.php');

if(canAccess()):

$currentuser = stripIDIR($_SERVER["REMOTE_USER"]);
$now = date('YmdHis');
$ccode = $_POST['CourseCode'];
$course = getCourse($ccode);
$dates = $_POST['StartDate'];
$count = 0;
$allclasses = [];
foreach($dates as $date) {

	$classid = date('YmdHis') . '-' . $count;
	$combinedtimes = h($_POST['StartTime'][$count]) . ' - ' . h($_POST['EndTime'][$count]);	
	$status = 'Requested';
	$coursedays = $course[6];
	if($coursedays < 1) $coursedays = 1;
	$coursecorrect = $coursedays - 1;
	$enddate = date("Y-m-d", strtotime($date . ' + ' . $coursecorrect . ' days'));

	if($course[21] == 'Classroom' || $course[21] == 'Blended') {
		$doweship = 'To Ship';
	} else {
		$doweship = 'No Ship';
	}
	$facilitatorsclean = '';
	$fac = $_POST['Facilitating'] ?? '';
	if(!empty($fac)) { 
		$fa = strip_tags(trim($fac));
		$facilitators = str_replace('@','',$fa);
		$facilitatorsclean = str_replace(',','',$facilitators);
	}
	if($fromform['VenueCity']) {
		$city = explode('TBD - ', $fromform['VenueCity']);
		$city = preg_replace('/\s+/', '', $city[1]);
		$city = str_replace('.', '', $city);
		$city = strtolower($city);
	} else {
		$city = 'N/A';
	}

	$newclass = Array($classid,
				$status,
				$now,
				$currentuser,
				'ELM',
				$course[0],
				$course[2],
				'', // ITEM code
				$date,
				$enddate,
				$combinedtimes,
				$_POST['MinEnroll'][$count], //$course[28], //MinEnroll
				$_POST['MaxEnroll'][$count], //$course[29], //MaxEnroll
				$shipdate,
				$facilitatorsclean,
				h($_POST['WebinarLink'][$count]),
				$date,
				$coursedays,
				'0', // Enrolled
				'0', // ReservedSeats
				'0', // PendingApproval
				'0', // Waitlisted
				'0', // Dropped
				'', // VenueID
				'', // VenueName
				$city, //h($fromform['VenueCity']),
				'', // VenueAddress
				'', // VenuePostalCode
				'', // VenueContactName
				'', // VenuePhone
				'', // VenueEmail
				'', // VenueAttention
				$_POST['RequestNotes'][$count], // h($fromform['RequestNotes']), $_POST['RequestNotes'];
				'', // Shipper
				'', // Boxes
				'', // Weight
				'', // Courier
				'', // TrackingOut
				'', // TrackingIn
				'', // AttendanceReturned
				'', // EvaluationsReturned
				'', // VenueNotified
				$now,
				$currentuser,
				'', // Assigned
				$course[21],
				$course[20], //h($fromform['CourseCategory']),
				'', // Region
				'', // CheckedBy
				$doweship, // ShippingStatus
				'', // PickupIn
				'', // avAssigned
				0, // venueCost
				'', // venueBEO
				h($_POST['StartTime'][$count]), // StartTime
				h($_POST['EndTime'][$count]), // EndTime
				$course[32], // CourseColor,
				'', // EvaluationsSent
				'' // EvaluationsLink
	);

	array_push($allclasses,$newclass);

	$count ++;
}

$fp = fopen('data/classes.csv', 'a+');
foreach ($allclasses as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
//header('Location: /lsapp/course.php?courseid=' . $course[0]);
header('Location: /lsapp/person.php?idir=' . stripIDIR($_SERVER["REMOTE_USER"]));

else:
	include('templates/noaccess.php');
endif;
