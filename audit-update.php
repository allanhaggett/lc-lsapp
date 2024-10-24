<?php 

opcache_reset();

$modified = date('Y-m-d:His');
$norm = explode('\\', $_SERVER["REMOTE_USER"]); 
$modifiedBy = strtolower($norm[1]); // ahaggett


// Rename the existing copy with a modified timestamp on the end 
// of the filename 

// audit-shamitch-20230720-161848
$existingname = $_POST['AuditID'];
$backupname = $existingname . '-modified-' . date('YmdHis');
$backuppath = 'data/backups/' . $backupname . '.json';
$existingpath = 'data/backups/' . $existingname . '.json';
rename($existingpath,$backuppath);



// $auditindex = [
// 	'Evaluationid' => $auditID,
// 	'Created' => date('Y-m-d H:i:s'),
// 	'CreatedBy' => $createdBy,
// 	'LSAppcourseID' => $_POST['lsappcourseid'], 
// 	'ResourceName' => $_POST['ResourceName'],
// 	'resourceType' => $_POST['resourceType'],
// 	'ResourceOwner' => $_POST['ResourceOwner'],
// 	'Status' => 'Submitted'
// ];
// $fp = fopen('data/backups/audits.csv', 'a+');
// fputcsv($fp, $auditindex);
// fclose($fp);




$newaudit = Array(
            'AuditID' => $existingname,
            'Status' => $_POST['Status'],
            'created' => $_POST['created'],
            'createdby' => $_POST['createdby'],
            'edited' => $modified,
            'editedby' => $modifiedBy,
            'resourceType' => $_POST['resourceType'],
            'LSAppCourseid' => $_POST['LSAppCourseid'],
            'ResourceName' => $_POST['ResourceName'],
            'ResourceOwner' => $_POST['ResourceOwner'],
            'DeliveryMethod' => $_POST['DeliveryMethod'],
            'Duration' => $_POST['Duration'],
            'Level' => $_POST['Level'],
            'Audience' => $_POST['Audience'],
            'Topic' => $_POST['Topic'],
            'OverallCourseOutcomes' => $_POST['OverallCourseOutcomes'],
            'Notes' => $_POST['Notes'],
            'MeasurableOutcomesForOrganization' => $_POST['MeasurableOutcomesForOrganization'],
            'CurrentOrganizationalMeasureBaseline' => $_POST['CurrentOrganizationalMeasureBaseline'],
            'LearningMetric' => $_POST['LearningMetric'],
            'SupportChangingSkills' => $_POST['SupportChangingSkills'],

            'BCPSPrincipleLearnerCentre' => $_POST['BCPSPrincipleLearnerCentre'],
            'BCPSPrincipleLearnerCentreSupportYes' => $_POST['BCPSPrincipleLearnerCentreSupportYes'],
            'BCPSPrincipleLearnerCentreSupportNo' => $_POST['BCPSPrincipleLearnerCentreSupportNo'],

            'BCPSPrincipleAlignedBusinessPriority' => $_POST['BCPSPrincipleAlignedBusinessPriority'],
            'BCPSPrincipleAlignedBusinessPrioritySupportYes' => $_POST['BCPSPrincipleAlignedBusinessPrioritySupportYes'],
            'BCPSPrincipleAlignedBusinessPrioritySupportNo' => $_POST['BCPSPrincipleAlignedBusinessPrioritySupportNo'],
            
            'BCPSPrincipleAvailableJIT' => $_POST['BCPSPrincipleAvailableJIT'],
            'BCPSPrincipleAvailableJITSupportYes' => $_POST['BCPSPrincipleAvailableJITSupportYes'],
            'BCPSPrincipleAvailableJITSupportNo' => $_POST['BCPSPrincipleAvailableJITSupportNo'],
            
            'BCPSPrincipleEmpowerGrowth' => $_POST['BCPSPrincipleEmpowerGrowth'],
            'BCPSPrincipleEmpowerGrowthSupportYes' => $_POST['BCPSPrincipleEmpowerGrowthSupportYes'],
            'BCPSPrincipleEmpowerGrowthSupportNo' => $_POST['BCPSPrincipleEmpowerGrowthSupportNo'],

            'BCPSPrinciplePromoteConnectness' => $_POST['BCPSPrinciplePromoteConnectness'],
            'BCPSPrinciplePromoteConnectnessSupportYes' => $_POST['BCPSPrinciplePromoteConnectnessSupportYes'],
            'BCPSPrinciplePromoteConnectnessSupportNo' => $_POST['BCPSPrinciplePromoteConnectnessSupportNo'],
            
            'BCPSPrincipleAnchorEstablishedContent' => $_POST['BCPSPrincipleAnchorEstablishedContent'],
            'BCPSPrincipleAnchorEstablishedContentSupportYes' => $_POST['BCPSPrincipleAnchorEstablishedContentSupportYes'],
            'BCPSPrincipleAnchorEstablishedContentSupportNo' => $_POST['BCPSPrincipleAnchorEstablishedContentSupportNo'],
            
            'BCPSPrincipleEncourageReflection' => $_POST['BCPSPrincipleEncourageReflection'],
            'BCPSPrincipleEncourageReflectionSupportYes' => $_POST['BCPSPrincipleEncourageReflectionSupportYes'],
            'BCPSPrincipleEncourageReflectionSupportNo' => $_POST['BCPSPrincipleEncourageReflectionSupportNo'],
            
            'BCPSPrincipleOverallPercent' => $_POST['BCPSPrincipleOverallPercent'],


            'MeetAccessibilityStandards' => $_POST['MeetAccessibilityStandards'],
            'MeetAccessibilityStandardsElaborate' => $_POST['MeetAccessibilityStandardsElaborate'],
            'MissingKeyContent' => $_POST['MissingKeyContent'],
            'MissingKeyContentElaborate' => $_POST['MissingKeyContentElaborate'],
            'ReduceRisk' => $_POST['ReduceRisk'],
            'ReduceRiskElaborate' => $_POST['ReduceRiskElaborate'],
            'SignificantReach' => $_POST['SignificantReach'],
            'SignificantReachElaborate' => $_POST['SignificantReachElaborate'],
            'WhatUpdates' => $_POST['WhatUpdates'],
            'WhatUpdatesElaborate' => $_POST['WhatUpdatesElaborate'],
            'UncompletedUpdateRisk' => $_POST['UncompletedUpdateRisk'],
            'ResourceRedirect' => $_POST['ResourceRedirect'],
            'ResourceRedirectElaborate' => $_POST['ResourceRedirectElaborate']
);
$audit = json_encode($newaudit);

$result = file_put_contents($existingpath, $audit);
if ($result !== false) {
	//echo "Data written to file successfully!";
} else {
	echo "Error writing to file!";
	exit;
}


$fromform = $_POST;

$f = fopen('data/backups/audits.csv','r');
$temp_table = fopen('data/backups/audits-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);
// AuditID,Created,CreatedBy,LSAppcourseID,ResourceName,resourceType,Status
$auditupdate = [
	'Auditid' => $existingname,
	'created' => $_POST['created'],
	'createdby' => $_POST['createdby'],
	'LSAppcourseID' => $_POST['LSAppCourseid'], 
	'ResourceName' => $_POST['ResourceName'],
	'resourceType' => $_POST['resourceType'],
	'Status' => $_POST['Status'],
    'PrinciplesPercent' => $_POST['BCPSPrincipleOverallPercent']
];

while (($data = fgetcsv($f)) !== FALSE){
    if($data[0] == $existingname) {
        fputcsv($temp_table,$auditupdate);
    } else {
        fputcsv($temp_table,$data);
    }
}

fclose($f);
fclose($temp_table);

rename('data/backups/audits-temp.csv','data/backups/audits.csv');



$go = 'Location: /learning/resource-review/review.php?auditid=' . $existingname;
header($go);


