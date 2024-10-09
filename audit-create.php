<?php 

$created = date('Y-m-d:His');
$norm = explode('\\', $_SERVER["REMOTE_USER"]); 
$createdBy = strtolower($norm[1]); // ahaggett
$auditID = 'review-' . $createdBy . '-' . date('Ymd-His');
//print_r($_POST); exit;
$newaudit = Array(
				'AuditID' => $auditID,
				'Status' => 'Draft',
				'created' => date('Y-m-d H:i:s'),
				'createdby' => $createdBy,
				'edited' => '',
				'editedby' => '',
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
				'AlignOrganizationalGoals' => $_POST['AlignOrganizationalGoals'],


				'BCPSPrincipleLearnerCentre' => $_POST['BCPSPrincipleLearnerCentre'],
				'BCPSPrincipleLearnerCentreSupportYes' => $_POST['BCPSPrincipleLearnerCentreSupportYes'],
				'BCPSPrincipleLearnerCentreSupportNo' => $_POST['BCPSPrincipleLearnerCentreSupportNo'],

				'BCPSPrincipleAlignedBusinessPriority' => $_POST['BCPSPrincipleAlignedBusinessPriority'],
				'BCPSPrincipleAlignedBusinessPrioritySupportYes' => $_POST['BCPSPrincipleAlignedBusinessPrioritySupportYes'],
				'BCPSPrincipleAlignedBusinessPrioritySupportNo' => $_POST['BCPSPrincipleAlignedBusinessPrioritySupportNo'],
				
				'BCPSPrincipleAvailableJIT' => $_POST['BCPSPrincipleAvailableJIT'],
				'BCPS Principle Available JIT SupportYes' => $_POST['BCPSPrincipleAvailableJITSupportYes'],
				'BCPS Principle Available JIT SupportNo' => $_POST['BCPSPrincipleAvailableJITSupportNo'],
				
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

$file = 'data/backups/' . $auditID . '.json';
$result = file_put_contents($file, $audit);
if ($result !== false) {
	//echo "Data written to file successfully!";
} else {
	echo "Error writing to file!";
	exit;
}
// AuditID,Created,CreatedBy,LSAppcourseID,ResourceName,resourceType,Status,PrinciplesPercent
$auditindex = [
	'AuditID' => $auditID,
	'Created' => date('Y-m-d H:i:s'),
	'CreatedBy' => $createdBy,
	'LSAppcourseID' => $_POST['LSAppCourseid'], 
	'ResourceName' => $_POST['ResourceName'],
	'resourceType' => $_POST['resourceType'],
	'Status' => 'Draft',
	'PrinciplesPercent' => $_POST['BCPSPrincipleOverallPercent']
];
$fp = fopen('data/backups/audits.csv', 'a+');
fputcsv($fp, $auditindex);
fclose($fp);

//header('Location: /lsapp/audits.php');
$go = 'Location: /learning/resource-review/review.php?auditid=' . $auditID;
header($go);

