<?php require('inc/lsapp.php') ?>
<?php if(canAccess()): ?>
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
X-WR-CALNAME: LC Firehose - All Classes for all courses
<?php $classdates = getClasses() ?>
<?php array_shift($classdates) ?>
<?php foreach($classdates as $class): ?>
<?php if($class[1] == 'Inactive' || $class[1] == 'Deleted') continue ?>
<?php 
$stime = '';//$class[54];
$etime = '';//$class[55];
if(!$stime) {
	$stime = '083000';
}
if(!$etime) {
	$etime = '163000';
} 
?>
BEGIN:VEVENT
UID:<?= $class[0] . PHP_EOL?>
DTSTAMP:<?= icalDate($class[8]) . 'T' . $stime . PHP_EOL ?>
ORGANIZER;CN=<?= $class[3] ?>:MAILTO:<?= $class[3] . PHP_EOL ?>
DTSTART:<?= icalDate($class[8]) . 'T' . $stime . PHP_EOL ?>
DTEND:<?= icalDate($class[9]) . 'T' . $etime . PHP_EOL ?>
SUMMARY:<?php echo  h($class[6]) . ' ' . h($class[14]) . ' facilitating' . PHP_EOL ?>
LOCATION:<?php echo  h($class[25]) . ' - ' . h($class[24]) . PHP_EOL ?>
<?php 
$facs = 'Facilitating: ';
$facils = explode(' ', $class[14]);
foreach ($facils as $f) {
	$facs .= '<a href="https://gww.bcpublicservice.gov.bc.ca/lsapp/person.php?idir=' . h($f) .'">' . h($f) .'</a> ';
}
?>
X-ALT-DESC;FMTTYPE=text/html:<?php echo $facs . '<br>' .h($class[10]) .'<br>'.h($class[7]) .'<br>Enrolled: '.h($class[18]) .'<br><a href="https://gww.bcpublicservice.gov.bc.ca/lsapp/class.php?classid='.h($class[0]) .'">' . h($class[6]) . '</a><br>Request notes:' . h($class[32]) . PHP_EOL ?>
X-MICROSOFT-CDO-BUSYSTATUS:BUSY
X-MICROSOFT-CDO-IMPORTANCE:1
X-MICROSOFT-CDO-INTENDEDSTATUS:BUSY
END:VEVENT<?= PHP_EOL ?>
<?php endforeach ?>
END:VCALENDAR
<?php else: ?>
Please contact learning.support.admin@gov.bc.ca for access to this calendar.
<?php endif ?>