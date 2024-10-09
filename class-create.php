<?php 
require('inc/lsapp.php');

if(canAccess()):

$fromform = $_POST;

if(!empty($fromform['CourseCode'])):

$startdate = h($fromform['StartDate']);
$urldate = str_replace('-', '', $startdate);
$ccode = h($fromform['CourseCode']);
$course = getCourse($ccode);

if($fromform['DeliveryMethod'] == 'Classroom' || $fromform['DeliveryMethod'] == 'Blended') {
	$doweship = 'To Ship';
} else {
	$doweship = 'No Ship';
}

$fa = strip_tags(trim($fromform['Facilitating']));
$facilitators = str_replace('@','',$fa);
$facilitatorsclean = str_replace(',','',$facilitators);

if($fromform['VenueCity']) {
	$city = explode('TBD - ', $fromform['VenueCity']);
	$city = preg_replace('/\s+/', '', $city[1]);
	$city = str_replace('.', '', $city);
	$city = strtolower($city);
} else {
	$city = 'N/A';
}

//$classid = $course[3] . '-' . $urldate . '-' . $city . '-' . stripIDIR($_SERVER["REMOTE_USER"]) . '-' . date('Ymd-His');
$classid = date('YmdHis');

//$mm = explode('/',$fromform['MinMax']);

$ded = 'ELM';
if(isset($fromform['Dedicated'])) {
	$ded = 'Dedicated';
}

$status = 'Requested';
if(isset($fromform['Draft'])) {
	$status = 'Draft';
}

$coursedays = h($fromform['CourseDays']);
if($coursedays < 1) $coursedays = 1;

// Wny is this here? What problem does it solve? I shoulda comented it when I made it!
// I _think_ that it's something to do with Outlook calendar display, but I can't recall.
// [03-Aug-2021 07:17:21 America/Los_Angeles] PHP Notice:  A non well formed numeric value encountered in E:\WebSites\Prod\BCPSA\Intranet\wwwroot\lsapp\class-create.php on line 52 
$coursecorrect = $coursedays - 1;

$enddate = date("Y-m-d", strtotime($startdate . ' + ' . $coursecorrect . ' days'));
$regiondays = 7;
$shipdate = date("Y-m-d", strtotime($startdate . ' - ' . $regiondays . ' days'));

$combinedtimes = h($fromform['StartTime']) . ' - ' . h($fromform['EndTime']);
// TODO this doesn't really work because my javascript insertion of d2-date isn't being picked up
// here for some odd reason. Figure this out and variable-session class offerings are yours.
if(isset($fromform['d2-date'])) {
	$csession = h($fromform['d2-date']);
	$sessID = 'sess-' . stripIDIR($_SERVER["REMOTE_USER"]) . '-' . date('Ymd-His');
	$sess = array($sessID,$classid,$csession);
	$sesp = fopen('data/sessions.csv', 'a+');
	foreach ($sess as $fields) {
		fputcsv($sessp, $fields);
	}
	fclose($sessp);
}

$newclass = Array($classid,
				$status,
				h($fromform['Requested']),
				h($fromform['RequestedBy']),
				$ded,
				h(trim($fromform['CourseCode'])),
				h($fromform['Course']),
				'', // ITEM code
				$startdate,
				$enddate,
				$combinedtimes,
				h($fromform['Min']), //MinEnroll
				h($fromform['Max']), //MaxEnroll
				$shipdate,
				h($facilitatorsclean),
				h($fromform['WebinarLink']),
				h($fromform['WebinarDate']),
				$coursedays,
				'0', // Enrolled
				'0', // ReservedSeats
				'0', // PendingApproval
				'0', // Waitlisted
				'0', // Dropped
				'', // VenueID
				'', // VenueName
				h($fromform['VenueCity']),
				'', // VenueAddress
				'', // VenuePostalCode
				'', // VenueContactName
				'', // VenuePhone
				'', // VenueEmail
				'', // VenueAttention
				h($fromform['RequestNotes']),
				'', // Shipper
				'', // Boxes
				'', // Weight
				'', // Courier
				'', // TrackingOut
				'', // TrackingIn
				'', // AttendanceReturned
				'', // EvaluationsReturned
				'', // VenueNotified
				h($fromform['Modified']),
				h($fromform['ModifiedBy']),
				'', // Assigned
				h($fromform['DeliveryMethod']),
				h($fromform['CourseCategory']),
				h($fromform['Region']),
				'', // CheckedBy
				$doweship, // ShippingStatus
				'', // PickupIn
				'', // avAssigned
				0, // venueCost
				'', // venueBEO
				h($fromform['StartTime']), // StartTime
				h($fromform['EndTime']), // EndTime
				$course[32], // CourseColor,
				'', // EvaluationsSent
				$course[35] // EvaluationsLink
	);


// As of 2020-12-01 (added 57-EvaluationsSent,58-EvaluationsLink) 
// 0-ClassID,1-Status,2-RequestedOn,3-RequestedBy,4-Dedicated,5-CourseID,6-CourseName,7-ItemCode,8-ClassDate,9-EndDate,10-ClassTimes,
// 11-MinEnroll,12-MaxEnroll,13-ShipDate,14-Facilitating,
// 15-WebinarLink,16-WebinarDate,17-ClassDays,18-Enrollment,19-ReservedSeats,20-pendingApproval,21-Waitlisted,22-Dropped,
// 23-VenueID,24-Venue,25-City,26-Address,27-ZIPPostal,28-ContactName,29-BusinessPhone,30-email,
// 31-VenueAttention,32-Notes,33- Shipper,34-Boxes,35-Weight,36-Courier,37-TrackingOut,38-TrackingIn,
// 39-Attendance,40-EvaluationsReturned,41-VenueNotified,42-Modified,43-ModifiedBy,44-Assigned,
// 45-DeliveryMethod,46-CourseCategory,47-tblClasses.Region,
// 48-CheckedBy,49-ShippingStatus,50-PickupIn,
// 51-avAssigned,venueCost,venueBEO,StartTime,55-EndTime,56-CourseColor,57-EvaluationsSent,58-EvaluationsLink

$class = array($newclass);
//print_r($class);
$fp = fopen('data/classes.csv', 'a+');
foreach ($class as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
header('Location: /lsapp/person.php?idir=' . stripIDIR($_SERVER["REMOTE_USER"]));

else:

	echo 'We\'re very sorry, but an error has occured. Please <a href="class-request.php">go back</a>, refresh the page and try again.';
	echo 'If this error persists, please contact learning.centre.admin@gov.bc.ca Thank you!';

endif;

else:
include('templates/noaccess.php');
endif;


