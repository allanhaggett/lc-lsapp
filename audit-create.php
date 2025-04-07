<?php 

$created = date('Y-m-d:His');
$norm = explode('\\', $_SERVER["REMOTE_USER"]); 
$createdBy = strtolower($norm[1]); // ahaggett
$auditID = 'review-' . $createdBy . '-' . date('Ymd-His');

// Sanitize input function
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to sanitize array inputs
function sanitize_array($array) {
    if (is_array($array)) {
        return array_map('sanitize_input', $array);
    }
    return [];
}

// Sanitize all incoming POST data
$sanitized_post = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
if (!$sanitized_post) {
    die("Invalid input data.");
}

// Build sanitized audit data
$newaudit = [
    'AuditID' => $auditID,
    'Status' => 'Draft',
    'created' => date('Y-m-d H:i:s'),
    'createdby' => $createdBy,
    'edited' => '',
    'editedby' => '',
    'resourceType' => sanitize_input($sanitized_post['resourceType']),
    'LSAppCourseid' => sanitize_input($sanitized_post['LSAppCourseid']),
    'ResourceName' => sanitize_input($sanitized_post['ResourceName']),
    'ResourceOwner' => sanitize_input($sanitized_post['ResourceOwner']),
    'DeliveryMethod' => sanitize_input($sanitized_post['DeliveryMethod']),
    'Duration' => sanitize_input($sanitized_post['Duration']),
    'Level' => sanitize_input($sanitized_post['Level']),
    'Audience' => sanitize_input($sanitized_post['Audience']),
    'Topic' => sanitize_input($sanitized_post['Topic']),
    'OverallCourseOutcomes' => sanitize_input($sanitized_post['OverallCourseOutcomes']),
    'Notes' => sanitize_input($sanitized_post['Notes']),
    'MeasurableOutcomesForOrganization' => sanitize_input($sanitized_post['MeasurableOutcomesForOrganization']),
    'CurrentOrganizationalMeasureBaseline' => sanitize_input($sanitized_post['CurrentOrganizationalMeasureBaseline']),
    'LearningMetric' => sanitize_input($sanitized_post['LearningMetric']),
    'SupportChangingSkills' => sanitize_input($sanitized_post['SupportChangingSkills']),

    'BCPSPrincipleLearnerCentre' => sanitize_array($sanitized_post['BCPSPrincipleLearnerCentre'] ?? []),
    'BCPSPrincipleLearnerCentreSupportYes' => sanitize_input($sanitized_post['BCPSPrincipleLearnerCentreSupportYes']),
    'BCPSPrincipleLearnerCentreSupportNo' => sanitize_input($sanitized_post['BCPSPrincipleLearnerCentreSupportNo']),

    'BCPSPrincipleAlignedBusinessPriority' => sanitize_array($sanitized_post['BCPSPrincipleAlignedBusinessPriority'] ?? []),
    'BCPSPrincipleAlignedBusinessPrioritySupportYes' => sanitize_input($sanitized_post['BCPSPrincipleAlignedBusinessPrioritySupportYes']),
    'BCPSPrincipleAlignedBusinessPrioritySupportNo' => sanitize_input($sanitized_post['BCPSPrincipleAlignedBusinessPrioritySupportNo']),
    
    'BCPSPrincipleAvailableJIT' => sanitize_array($sanitized_post['BCPSPrincipleAvailableJIT'] ?? []),
    'BCPSPrincipleAvailableJITSupportYes' => sanitize_input($sanitized_post['BCPSPrincipleAvailableJITSupportYes']),
    'BCPSPrincipleAvailableJITSupportNo' => sanitize_input($sanitized_post['BCPSPrincipleAvailableJITSupportNo']),
    
    'BCPSPrincipleEmpowerGrowth' => sanitize_array($sanitized_post['BCPSPrincipleEmpowerGrowth'] ?? []),
    'BCPSPrincipleEmpowerGrowthSupportYes' => sanitize_input($sanitized_post['BCPSPrincipleEmpowerGrowthSupportYes']),
    'BCPSPrincipleEmpowerGrowthSupportNo' => sanitize_input($sanitized_post['BCPSPrincipleEmpowerGrowthSupportNo']),

    'BCPSPrinciplePromoteConnectness' => sanitize_array($sanitized_post['BCPSPrinciplePromoteConnectness'] ?? []),
    'BCPSPrinciplePromoteConnectnessSupportYes' => sanitize_input($sanitized_post['BCPSPrinciplePromoteConnectnessSupportYes']),
    'BCPSPrinciplePromoteConnectnessSupportNo' => sanitize_input($sanitized_post['BCPSPrinciplePromoteConnectnessSupportNo']),
    
    'BCPSPrincipleAnchorEstablishedContent' => sanitize_array($sanitized_post['BCPSPrincipleAnchorEstablishedContent'] ?? []),
    'BCPSPrincipleAnchorEstablishedContentSupportYes' => sanitize_input($sanitized_post['BCPSPrincipleAnchorEstablishedContentSupportYes']),
    'BCPSPrincipleAnchorEstablishedContentSupportNo' => sanitize_input($sanitized_post['BCPSPrincipleAnchorEstablishedContentSupportNo']),
    
    'BCPSPrincipleEncourageReflection' => sanitize_array($sanitized_post['BCPSPrincipleEncourageReflection'] ?? []),
    'BCPSPrincipleEncourageReflectionSupportYes' => sanitize_input($sanitized_post['BCPSPrincipleEncourageReflectionSupportYes']),
    'BCPSPrincipleEncourageReflectionSupportNo' => sanitize_input($sanitized_post['BCPSPrincipleEncourageReflectionSupportNo']),

    'BCPSPrincipleOverallPercent' => sanitize_input($sanitized_post['BCPSPrincipleOverallPercent']),

    'MeetAccessibilityStandards' => sanitize_input($sanitized_post['MeetAccessibilityStandards']),
    'MeetAccessibilityStandardsElaborate' => sanitize_input($sanitized_post['MeetAccessibilityStandardsElaborate']),
    'MissingKeyContent' => sanitize_input($sanitized_post['MissingKeyContent']),
    'MissingKeyContentElaborate' => sanitize_input($sanitized_post['MissingKeyContentElaborate']),
    'ReduceRisk' => sanitize_input($sanitized_post['ReduceRisk']),
    'ReduceRiskElaborate' => sanitize_input($sanitized_post['ReduceRiskElaborate']),
    'SignificantReach' => sanitize_input($sanitized_post['SignificantReach']),
    'SignificantReachElaborate' => sanitize_input($sanitized_post['SignificantReachElaborate']),
    'WhatUpdates' => sanitize_input($sanitized_post['WhatUpdates']),
    'WhatUpdatesElaborate' => sanitize_input($sanitized_post['WhatUpdatesElaborate']),
    'UncompletedUpdateRisk' => sanitize_input($sanitized_post['UncompletedUpdateRisk']),
    'ResourceRedirect' => sanitize_input($sanitized_post['ResourceRedirect']),
    'ResourceRedirectElaborate' => sanitize_input($sanitized_post['ResourceRedirectElaborate'])
];

$audit = json_encode($newaudit);

$file = 'data/backups/' . $auditID . '.json';
$result = file_put_contents($file, $audit);

if ($result === false) {
    die("Error writing to file!");
}

// AuditID,Created,CreatedBy,LSAppcourseID,ResourceName,resourceType,Status,PrinciplesPercent
$auditindex = [
    'AuditID' => $auditID,
    'Created' => date('Y-m-d H:i:s'),
    'CreatedBy' => $createdBy,
    'LSAppcourseID' => sanitize_input($sanitized_post['LSAppCourseid']), 
    'ResourceName' => sanitize_input($sanitized_post['ResourceName']),
    'resourceType' => sanitize_input($sanitized_post['resourceType']),
    'Status' => 'Draft',
    'PrinciplesPercent' => sanitize_input($sanitized_post['BCPSPrincipleOverallPercent'])
];

$fp = fopen('data/backups/audits.csv', 'a+');
fputcsv($fp, $auditindex);
fclose($fp);

header('Location: /learning/resource-review/review.php?auditid=' . $auditID);
