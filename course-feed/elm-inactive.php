<?php
opcache_reset();
require('../inc/lsapp.php');
if (canAccess()):

// Helper function to load courses from CSV
function getCoursesFromCSV($filepath, $itemCodeIndex = 0) {
    $courses = [];
    if (($handle = fopen($filepath, 'r')) !== false) {
        fgetcsv($handle); // Skip header row
        while ($row = fgetcsv($handle)) {
            $courses[strtoupper($row[$itemCodeIndex])] = $row;
        }
        fclose($handle);
    }
    return $courses;
}

// Paths to course data
$coursesPath = build_path(BASE_DIR, 'data', 'courses.csv');
$hubCoursesPath = build_path(BASE_DIR, 'course-feed', 'data', 'courses.csv');

// Load LSApp courses and Hub courses
$lsappCourses = getCoursesFromCSV($coursesPath, 4);  // Item Code is at index 4
$hubCourses = getCoursesFromCSV($hubCoursesPath, 0); // Item Code is at index 0

$logEntries = [];
$timestamp = date('YmdHis');
$logFilePath = build_path(BASE_DIR, 'data', "elminactive-log-$timestamp.log");

// Prepare updated courses array
$updatedCourses = $lsappCourses;

foreach ($lsappCourses as $lsappCode => $lsappCourse) {
    // Check if course is part of PSA Learning System and doesn't exist in hubCourses
    if ($lsappCourse[52] === 'PSA Learning System' && !isset($hubCourses[$lsappCode])) {
        // Mark course as inactive and update HUBInclude to "No"
        $updatedCourses[$lsappCode][1] = 'Inactive'; // Set status to Inactive
        $updatedCourses[$lsappCode][53] = 'No';      // Set HUBInclude to No
        $logEntries[] = "Marked course '{$lsappCourse[2]}' (Item Code: $lsappCode) as Inactive and set HUBInclude to No";
    }
}

// Write log entries to the log file
file_put_contents($logFilePath, implode("\n", $logEntries) . "\n", FILE_APPEND);

// Write updated courses to a temporary CSV file
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
        'ELMCourseID', 'Modified','Platform','HUBInclude',
        'RegistrationLink','CourseNameSlug','HubExpirationDate'
    ]);

    // Write all courses to the temporary file
    foreach ($updatedCourses as $course) {
        fputcsv($fpTemp, $course);
    }
    
    fclose($fpTemp);

    // Replace the original courses file with the updated temporary file
    rename($tempFilePath, $coursesPath);
}
header('Location: feed-create.php');
?>
<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>