<?php 

require('inc/lsapp.php');

$fromform = $_POST;

$createdby = LOGGED_IN_IDIR;
$now = date('Y-m-d H:i:s');
$templateID = date('YmdHis');
// CourseID,TemplateName,Template,Created,CreatedBy,Modified,ModifiedBy
$newtemplate = Array($templateID,
				h($fromform['CourseID']),
				h($fromform['TemplateName']),
				h($fromform['Template']),
				$now,
				$createdby,
				$now,
				$createdby
			);

$template = array($newtemplate);
$fp = fopen('data/communication-templates.csv', 'a+');
foreach ($template as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
exit;
header('Location: /lsapp/course.php?courseid=' . $fromform['ClassID']);
?>