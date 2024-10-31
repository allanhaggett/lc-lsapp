<?php 
require('inc/lsapp.php');
opcache_reset();
if(canAccess()):

	$fromform = $_POST;

	$courseid = date('YmdHis');
	$now = date('Y-m-d\TH:i:s');


	
	// if(!isset($fromform['Category'])) {
	// 	echo 'Please assign at least one category to this course.<br>';
	// 	echo 'If there is not a category that fits, please email Operations before proceeding.';
	// 	exit;
	// }
	$coursecat = '';
	// foreach($fromform['Category'] as $c) {
	// 	$coursecat .= $c . ',';
	// }
	$weship = 'Yes';
	if(!isset($fromform['WeShip'])) {
		$weship = 'No';
	}
	$alchemer = 'Yes';
	if(!isset($fromform['Alchemer'])) {
		$alchemer = 'No';
	}
	$combinedtimes = h($fromform['StartTime']) . ' - ' . h($fromform['EndTime']);
	$newcourse = Array($courseid,
					'Requested',
					h($fromform['CourseName']),
					h($fromform['CourseShort']),
					'', // ItemCode,
					$combinedtimes,
					h($fromform['ClassDays']),
					'', // ELM link
					h($fromform['PreWork']),
					h($fromform['PostWork']),
					h($fromform['CourseOwner']),
					'', // used to be min/max now unused
					h($fromform['CourseNotes']),
					h($fromform['Requested']),
					h($fromform['RequestedBy']),
					h($fromform['EffectiveDate']),
					h($fromform['CourseDescription']),
					h($fromform['CourseAbstract']),
					h($fromform['Prerequisites']),
					h($fromform['Keywords']),
					$coursecat,
					h($fromform['Method']),
					h($fromform['elearning']),
					$weship,
					'', // ProjectNumber
					'', // Responsibility
					'', // ServiceLine
					'', // STOB
					h($fromform['MinEnroll']),
					h($fromform['MaxEnroll']),
					h($fromform['StartTime']),
					h($fromform['EndTime']),
					'#F1F1F1',
					1,
					h($fromform['Developer']),
					h($fromform['EvaluationsLink']),
					h($fromform['LearningHubPartner']),
					$alchemer,
					h($fromform['Topics']),
					h($fromform['Audience']),
					h($fromform['Levels']),
					h($fromform['Reporting']),
					'', // PathLAN
					'', // PathStaging
					'', // PathLive
					'', // PathNIK
					'', // PathTeams
					0, // isMoodle,
					0, // TaxProcessed
					'', // TaxProcessedBy
					'',  // ELMCourseID - field #50
					$now,
					'', // External System
					0 // HUBInclude
		);
		
	$course = array($newcourse);
	$fp = fopen('data/courses.csv', 'a+');
	foreach ($course as $fields) {
		fputcsv($fp, $fields);
	}
	fclose($fp);

	// CourseID,Role,IDIR,Date
	$peoplefp = fopen('data/course-people.csv', 'a+');
	$stew = [$courseid,'steward',$fromform['CourseOwner'], $now];
	fputcsv($peoplefp, $stew);

	$dev = [$courseid,'dev',$fromform['Developer'], $now];
	fputcsv($peoplefp, $dev);

	fclose($peoplefp);

	header('Location: /lsapp/course.php?courseid=' . $courseid);
else:
	include('templates/noaccess.php');
endif;