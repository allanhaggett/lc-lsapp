<?php
opcache_reset();
require('../inc/lsapp.php');
if (canAccess()):

function getCoursesFromCSV($filepath, $isActiveFilter = false, $itemCodeIndex = 0) {
    $courses = [];
    if (($handle = fopen($filepath, 'r')) !== false) {
        fgetcsv($handle); // Skip header row
        while ($row = fgetcsv($handle)) {
            $itemCode = strtoupper(trim($row[$itemCodeIndex])); // Normalize Item Code
            if (!$isActiveFilter || $row[1] === 'Active' || $row[1] === 'Inactive') {
                $courses[$itemCode] = $row;
            }
        }
        fclose($handle);
    }
    return $courses;
}

function updateCourse($existingCourse, $newCourseData, &$logEntries) {
    $updatedCourse = $existingCourse;

    $fieldMappings = [
        2  => 1,  // CourseName (ELM index 1 -> LSApp index 2)
        16 => 2,  // CourseDescription (ELM index 2 -> LSApp index 16)
        19 => 12, // Keywords (ELM index 12 -> LSApp index 19)
        21 => 3,  // Method (ELM index 3 -> LSApp index 21)
        36 => 10, // LearningHubPartner (ELM index 10 -> LSApp index 36)
        38 => 15, // Topics (ELM index 15 -> LSApp index 38)
        39 => 14, // Audience (ELM index 14 -> LSApp index 39)
        40 => 13, // Levels (Group) (ELM index 13 -> LSApp index 40)
    ];

    $changes = [];

    foreach ($fieldMappings as $lsappIndex => $elmIndex) {
        $existingValue = mb_strtolower(trim(html_entity_decode($existingCourse[$lsappIndex])));
        $newValue = mb_strtolower(trim(html_entity_decode($newCourseData[$elmIndex] ?? '')));

        if ($existingValue !== $newValue) {
            $updatedCourse[$lsappIndex] = h($newCourseData[$elmIndex] ?? '');
            $changes[] = "Updated field index $lsappIndex to '{$updatedCourse[$lsappIndex]}'";
            $updatedCourse[51] = date('Y-m-d\TH:i:s');
        }
    }

    // Update status, platform, and HUBInclude conditionally
    if ($existingCourse[1] === 'Inactive') {
        $updatedCourse[1] = 'Active';
        $changes[] = "Updated status to 'Active'";
    }

    if (mb_strtolower(trim($existingCourse[52])) !== 'psa learning system') {
        $updatedCourse[52] = 'PSA Learning System';
        $changes[] = "Updated Platform to 'PSA Learning System'";
    }

    if ($existingCourse[53] != 'Yes') {
        $updatedCourse[53] = 'Yes';
        $changes[] = "Updated HUBInclude to Yes";
    }

    if ($changes) {
        $logEntries[] = "Updated course '{$existingCourse[2]}' (Item Code: {$existingCourse[4]}) with changes:\n  " . implode("\n  ", $changes);
    }

    return $updatedCourse;
}

$coursesPath = build_path(BASE_DIR, 'data', 'courses.csv');
$hubCoursesPath = build_path(BASE_DIR, 'course-feed', 'data', 'courses.csv');
$timestamp = date('YmdHis');
$logEntries = [];
$logFilePath = build_path(BASE_DIR, 'data', "course-sync-log-$timestamp.log");
$inactiveLogFilePath = build_path(BASE_DIR, 'data', "elminactive-log-$timestamp.log");

$lsappCourses = getCoursesFromCSV($coursesPath, false, 4);
$hubCourses = getCoursesFromCSV($hubCoursesPath, false, 0);

$updatedCourses = [];
$count = 0;
foreach ($hubCourses as $hcCode => $hc) {
    if (isset($lsappCourses[$hcCode])) {
        $updatedCourse = updateCourse($lsappCourses[$hcCode], $hc, $logEntries);
        $itemCode = $updatedCourse[4];
        $updatedCourses[$itemCode] = $updatedCourse;
    } else {
        $courseId = $timestamp . '-' . ++$count;
        $slug = createSlug($hc[1]);
        $newCourse = [
            $courseId,
            'Active',
            h($hc[1] ?? ''),        // CourseName
            '',                     // CourseShort
            h($hc[0] ?? ''),        // ItemCode
            '', '', '', '', '',     // ClassTimes, ClassDays, ELM, PreWork, PostWork
            '',                     // CourseOwner
            '', '',                 // MinMax, CourseNotes,
            $timestamp,             // Requested
            'SYNCBOT',              // RequestedBy
            $timestamp,             // EffectiveDate
            h($hc[2] ?? ''),        // CourseDescription
            '', '',                 // CourseAbstract, Prerequisites
            h($hc[12] ?? ''),       // Keywords
            '',                     // Category
            h($hc[3] ?? ''),        // Method
            '', 'No',               // elearning, WeShip
            '', '', '', '', '',     // ProjectNumber, Responsibility, ServiceLine, STOB, MinEnroll
            '', '', '',             // MaxEnroll, StartTime, EndTime
            '#F1F1F1',              // Color
            1,                      // Featured
            '', '',                 // Developer, EvaluationsLink
            h($hc[10] ?? ''),       // LearningHubPartner
            'No',                   // Alchemer
            h($hc[15] ?? ''),       // Topics
            h($hc[14] ?? ''),       // Audience
            h($hc[13] ?? ''),       // Levels (Group)
            '', '', '', '', '',     // Reporting, PathLAN, PathStaging, PathLive, PathNIK
            '',                     // PathTeams
            0,                      // isMoodle
            0, '',                  // TaxProcessed, TaxProcessedBy
            h($hc[11] ?? ''),       // ELMCourseID
            $timestamp,             // Modified
            'PSA Learning System',  // Platform
            'Yes',                  // HUBInclude
            '',                     // RegistrationLink
            $slug,                  // CourseNameSlug
            ''                      // HubExpirationDate
        ];
        $itemCode = $newCourse[4];
        $updatedCourses[$itemCode] = $newCourse;
        $logEntries[] = "Added new course '{$newCourse[2]}' (Item Code: {$newCourse[4]})";
    }
}

foreach ($lsappCourses as $lsappCode => $lsappCourse) {
    // $lsappCourse[53] == 1 || $lsappCourse[53] == 'Yes' && 
    if ($lsappCourse[52] === 'PSA Learning System' && !isset($hubCourses[$lsappCode])) {
        $lsappCourse[53] = 'No';
        $updatedCourses[$lsappCode] = $lsappCourse;
        $logEntries[] = "Set HUBInclude to No for '{$lsappCourse[2]}' (Class code: $lsappCode)";
    }
}

file_put_contents($logFilePath, implode("\n", $logEntries) . "\n", FILE_APPEND);

$tempFilePath = build_path(BASE_DIR, 'data', 'temp_courses.csv');
$fpTemp = fopen($tempFilePath, 'w');
if ($fpTemp !== false) {
    fputcsv($fpTemp, [
        'CourseID', 'Status', 'CourseName', 'CourseShort', 'ItemCode', 'ClassTimes',
        'ClassDays', 'ELM', 'PreWork', 'PostWork', 'CourseOwner', 'MinMax', 'CourseNotes',
        'Requested', 'RequestedBy', 'EffectiveDate', 'CourseDescription', 'CourseAbstract',
        'Prerequisites', 'Keywords', 'Category', 'Method', 'elearning', 'WeShip', 'ProjectNumber',
        'Responsibility', 'ServiceLine', 'STOB', 'MinEnroll', 'MaxEnroll', 'StartTime', 'EndTime',
        'Color', 'Featured', 'Developer', 'EvaluationsLink', 'LearningHubPartner', 'Alchemer',
        'Topics', 'Audience', 'Levels', 'Reporting', 'PathLAN', 'PathStaging', 'PathLive',
        'PathNIK', 'PathTeams', 'isMoodle', 'TaxProcessed', 'TaxProcessedBy', 'ELMCourseID',
        'Modified','Platform','HUBInclude', 'RegistrationLink','CourseNameSlug','HubExpirationDate'
    ]);

    foreach ($lsappCourses as $itemCode => $course) {
        fputcsv($fpTemp, $updatedCourses[$itemCode] ?? $course);
    }
    fclose($fpTemp);
    rename($tempFilePath, $coursesPath);
}
header('Location: feed-create.php');
?>
<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>