<?php
opcache_reset();
require('../inc/lsapp.php');
if (canAccess()):

function getCoursesFromCSV($filepath, $isActiveFilter = false, $itemCodeIndex = 0) {
    $courses = [];
    if (($handle = fopen($filepath, 'r')) !== false) {
        fgetcsv($handle); // Skip header row
        while ($row = fgetcsv($handle)) {
            // Check if active filter is applied and skip inactive rows if needed
            if (!$isActiveFilter || $row[1] === 'Active' || $row[1] === 'Inactive') {
                $courses[strtoupper($row[$itemCodeIndex])] = $row;
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

    // Set status to "Active" if it's currently "Inactive"
    if ($existingCourse[1] === 'Inactive') {
        $updatedCourse[1] = 'Active';
        $changes[] = "Updated status to 'Active'";
    }

    foreach ($fieldMappings as $lsappIndex => $elmIndex) {
        if ($existingCourse[$lsappIndex] !== h($newCourseData[$elmIndex] ?? '')) {
            $updatedCourse[$lsappIndex] = h($newCourseData[$elmIndex] ?? '');
            $changes[] = "Updated field index $lsappIndex to '{$updatedCourse[$lsappIndex]}'";
            $updatedCourse[51] = date('Y-m-d\TH:i:s');
        }
    }

    if ($existingCourse[52] !== 'PSA Learning System') {
        $updatedCourse[52] = 'PSA Learning System';
        $changes[] = "Updated Platform to 'PSA Learning System'";
    }
    if ($existingCourse[53] != 1) {
        $updatedCourse[53] = 1;
        $changes[] = "Updated HUBInclude to 1";
    }

    if ($changes) {
        $logEntries[] = "Updated course '{$existingCourse[2]}' (Item Code: {$existingCourse[4]}) with changes:\n  " . implode("\n  ", $changes);
    }

    return $updatedCourse;
}

// Paths to course data
$coursesPath = build_path(BASE_DIR, 'data', 'courses.csv');
$hubCoursesPath = build_path(BASE_DIR, 'course-feed', 'data', 'courses.csv');
$timestamp = date('YmdHis');
$now = date('Y-m-d\TH:i:s');

$logEntries = [];

// Generate log file path
$logFilePath = build_path(BASE_DIR, 'data', "course-sync-log-$timestamp.log");

// Load all courses (active and inactive)
$lsappCourses = getCoursesFromCSV($coursesPath, false, 4);
$hubCourses = getCoursesFromCSV($hubCoursesPath, false, 0);

$count = 0;
$updatedCourses = [];

foreach ($hubCourses as $hcCode => $hc) {
    if (isset($lsappCourses[$hcCode])) {
        // Update course if it exists
        $updatedCourse = updateCourse($lsappCourses[$hcCode], $hc, $logEntries);
        $itemCode = $updatedCourse[4];
        $updatedCourses[$itemCode] = $updatedCourse;
        
    } else {
        // Add new course if it doesn't exist
        $count++;
        $courseId = $timestamp . '-' . $count;

        $newCourse = [
            $courseId,
            'Active',
            h($hc[1] ?? ''),   // CourseName
            '',                 // CourseShort
            h($hc[0] ?? ''),    // ItemCode
            '', '', '', '', '', // ClassTimes, ClassDays, ELM, PreWork, PostWork
            '',                 // CourseOwner
            '', '',             // MinMax, CourseNotes,
            $now,               // Requested
            'SYNCBOT',          // RequestedBy
            $now,               // EffectiveDate
            h($hc[2] ?? ''),    // CourseDescription
            '', '',             // CourseAbstract, Prerequisites
            h($hc[12] ?? ''),   // Keywords
            '',                 // Category
            h($hc[3] ?? ''),    // Method
            '', 'No',           // elearning, WeShip
            '', '', '', '', '', // ProjectNumber, Responsibility, ServiceLine, STOB, MinEnroll
            '', '', '',         // MaxEnroll, StartTime, EndTime
            '#F1F1F1',          // Color
            1,                  // Featured
            '', '',             // Developer, EvaluationsLink
            h($hc[10] ?? ''),   // LearningHubPartner
            'No',               // Alchemer
            h($hc[15] ?? ''),   // Topics
            h($hc[14] ?? ''),   // Audience
            h($hc[13] ?? ''),   // Levels (Group)
            '', '', '', '', '', // Reporting, PathLAN, PathStaging, PathLive, PathNIK
            '',                 // PathTeams
            0,                  // isMoodle
            0, '',              // TaxProcessed, TaxProcessedBy
            h($hc[11] ?? ''),   // ELMCourseID
            $now,               // Modified
            'PSA Learning System', // Platform
            1                      // HUBInclude
        ];
        $itemCode = $newCourse[4];
        $updatedCourses[$itemCode] = $newCourse;
        $logEntries[] = "Added new course '{$newCourse[2]}' (Item Code: {$newCourse[4]})";
    }
}

// Write log entries to the log file
file_put_contents($logFilePath, implode("\n", $logEntries) . "\n", FILE_APPEND);

// Continue with file replacement logic as before
$tempFilePath = build_path(BASE_DIR, 'data', 'temp_courses.csv');
$fpTemp = fopen($tempFilePath, 'w');
if ($fpTemp !== false) {
    // Write the header row
    fputcsv($fpTemp, [
        'CourseID', 'Status', 'CourseName', 'CourseShort', 'ItemCode', 'ClassTimes', 
        'ClassDays', 'ELM', 'PreWork', 'PostWork', 'CourseOwner', 'MinMax', 
        'CourseNotes', 'Requested', 'RequestedBy', 'EffectiveDate', 'CourseDescription', 
        'CourseAbstract', 'Prerequisites', 'Keywords', 'Category', 'Method', 
        'elearning', 'WeShip', 'ProjectNumber', 'Responsibility', 'ServiceLine', 
        'STOB', 'MinEnroll', 'MaxEnroll', 'StartTime', 'EndTime', 'Color', 'Featured', 
        'Developer', 'EvaluationsLink', 'LearningHubPartner', 'Alchemer', 'Topics', 
        'Audience', 'Levels', 'Reporting', 'PathLAN', 'PathStaging', 'PathLive', 
        'PathNIK', 'PathTeams', 'isMoodle', 'TaxProcessed', 'TaxProcessedBy', 
        'ELMCourseID', 'Modified','Platform','HUBInclude'
    ]);

    if (($fpOriginal = fopen($coursesPath, 'r')) !== false) {
        fgetcsv($fpOriginal);
        
        while (($row = fgetcsv($fpOriginal)) !== false) {
            $itemCode = $row[4];
            if (isset($updatedCourses[$itemCode])) {
                fputcsv($fpTemp, $updatedCourses[$itemCode]);
                unset($updatedCourses[$itemCode]);
            } else {
                fputcsv($fpTemp, $row);
            }
        }
        fclose($fpOriginal);
    }                                                                                                                           
    
    foreach ($updatedCourses as $newCourse) {
        fputcsv($fpTemp, $newCourse);
    }

    fclose($fpTemp);
    rename($tempFilePath, $coursesPath);
}
header('Location: feed-create.php');
?>
<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>