<?php
opcache_reset();
require('../inc/lsapp.php');
if (canAccess()):

function getCoursesFromCSV($filepath, $isActiveFilter = false, $itemCodeIndex = 0) {
	$courses = [];
	if (($handle = fopen($filepath, 'r')) !== false) {
		fgetcsv($handle); // Skip header row
		while ($row = fgetcsv($handle)) {
			if (!$isActiveFilter || $row[1] === 'Active') {
				$courses[strtoupper($row[$itemCodeIndex])] = $row; // Use specified index as key
			}
		}
		fclose($handle);
	}
	return $courses;
}

function updateCourse($existingCourse, $newCourseData) {
    $updatedCourse = $existingCourse;

    // Define the field mappings between ELM and LSApp CSVs
    $fieldMappings = [
        2  => 1,  // CourseName (ELM index 1 -> LSApp index 2)
        16 => 2,  // CourseDescription (ELM index 2 -> LSApp index 16)
        19 => 12, // Keywords (ELM index 12 -> LSApp index 19)
        21 => 3,  // Method (ELM index 3 -> LSApp index 21)
        36 => 10, // LearningHubPartner (ELM index 10 -> LSApp index 36)
        38 => 15, // Topics (ELM index 15 -> LSApp index 38)
        39 => 14, // Audience (ELM index 14 -> LSApp index 39)
        40 => 13,  // Levels (Group) (ELM index 13 -> LSApp index 40)
    ];

    foreach ($fieldMappings as $lsappIndex => $elmIndex) {
        if ($existingCourse[$lsappIndex] !== h($newCourseData[$elmIndex] ?? '')) {
            $updatedCourse[$lsappIndex] = h($newCourseData[$elmIndex] ?? '');
            // Update the 'Modified' field to the current timestamp
            $updatedCourse[51] = date('Y-m-d\TH:i:s'); // Adjust as needed for your date format
        }
    }

    if ($existingCourse[52] !== 'PSA Learning System') {
        $updatedCourse[52] = 'PSA Learning System'; // It's always this from this source.
    }
    if ($existingCourse[53] != 1) {
        $updatedCourse[53] = 1; // It's always published if it's in this feed.
    }

    return $updatedCourse;
}

// Paths to course data
$coursesPath = build_path(BASE_DIR, 'data', 'courses.csv');
$hubCoursesPath = build_path(BASE_DIR, 'course-feed', 'data', 'courses.csv');

// Load courses with specified index for Item Code in each file
$lsappCourses = getCoursesFromCSV($coursesPath, true, 4);  // LSApp file, Item Code is at index 4
$hubCourses = getCoursesFromCSV($hubCoursesPath, false, 0); // ELM file, Item Code is at index 0
// echo '<pre>';
// / print_r($hubCourses); exit;
$timestamp = date('YmdHis');
$now = date('Y-m-d\TH:i:s');
$count = 0;

$updatedCourses = [];

foreach ($hubCourses as $hcCode => $hc) {

    if (isset($lsappCourses[$hcCode])) {
        // Update course if it exists and fields have changed
        $updatedCourse = updateCourse($lsappCourses[$hcCode], $hc);
        $itemCode = $updatedCourse[4]; // Assuming this is the ItemCode
        $updatedCourses[$itemCode] = $updatedCourse;
        
    } else {
        // Add new course if it doesn't exist
        $count++;
        $courseId = $timestamp . '-' . $count;
        // ELM courses.csv:
        //0-Course Code, 1-Course Name, 2-Course Description, 3-Delivery Method, 4-Category, 5-Learner Group, 6-Duration,
        //7-Available Classes, 8-Link to ELM Search, 9-Course Last Modified, 10-Course Owner Org, 11-Course ID, 12-Keywords, 13-Group, 14-Audience, 15-Topic
        //
        // LSApp courses.csv:
        //0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,10-CourseOwner,
        //11-MinMax,12-CourseNotes,13-Requested,14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,
        //19-Keywords,20-Categories,21-Method,22-elearning,23-WeShip,24-ProjectNumber,25-Responsibility,26-ServiceLine,
        //27-STOB,28-MinEnroll,29-MaxEnroll,30-StartTime,31-EndTime,32-Color
        //33-Featured,34-Developer,35-EvaluationsLink,36-LearningHubPartner,37-Alchemer,
        //38-Topics,39-Audience,40-Levels,41-Reporting
        //42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams
        // 47-isMoodle,48-TaxProcessed,49-TaxProcessedBy,50-ELMCourseID,51-Modified
        // 52-ExternalSystem, 53-HUBInclude

        $newCourse = [
            $courseId,
            'Active',
            h($hc[1] ?? ''),   // CourseName
            '',                 // CourseShort
            h($hc[0] ?? ''),    // ItemCode
            '', '', '', '', '', // ClassTimes, ClassDays, ELM, PreWork, PostWork
            h($hc[12] ?? ''),   // CourseOwner
            '', '', '',         // MinMax, CourseNotes, Requested
            'SYNCBOT',          // RequestedBy
            $now,               // EffectiveDate
            h($hc[2] ?? ''),    // CourseDescription
            '', '',             // CourseAbstract, Prerequisites
            h($hc[14] ?? ''),   // Keywords
            h($hc[4] ?? ''),    // Category
            h($hc[3] ?? ''),    // Method
            '', 'No',           // elearning, WeShip
            '', '', '', '', '', // ProjectNumber, Responsibility, ServiceLine, STOB, MinEnroll
            '', '', '',         // MaxEnroll, StartTime, EndTime
            '#F1F1F1',          // Color
            1,                  // Featured
            '', '',             // Developer, EvaluationsLink
            h($hc[10] ?? ''),   // LearningHubPartner
            'No',               // Alchemer
            h($hc[18] ?? ''),   // Topics
            h($hc[17] ?? ''),   // Audience
            h($hc[16] ?? ''),   // Levels
            '', '', '', '', '', // Reporting, PathLAN, PathStaging, PathLive, PathNIK
            '',                 // PathTeams
            0,                  // isMoodle
            0, '',              // TaxProcessed, TaxProcessedBy
            h($hc[13] ?? ''),   // ELMCourseID
            $now,                // Modified
            'PSA Learning System', // ExternalSystem
            1                      // HUBInclude
        ];
        $itemCode = $newCourse[4]; // ItemCode
        $updatedCourses[$itemCode] = $newCourse;
        echo h($hc[1] ?? '') . ' ADDED<br>';
    }
}

// File paths
$tempFilePath = build_path(BASE_DIR, 'data', 'temp_courses.csv');
// Write updated courses to a temporary CSV file
$fpTemp = fopen($tempFilePath, 'w');
if ($fpTemp !== false) {
    // Write the header row to the temporary file
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
        'ELMCourseID', 'Modified','ExternalSystem','HUBInclude'
    ]);

    // Load existing courses from the main file
    if (($fpOriginal = fopen($coursesPath, 'r')) !== false) {
        fgetcsv($fpOriginal); // Skip header row in the original file
        
        while (($row = fgetcsv($fpOriginal)) !== false) {
            $itemCode = $row[4]; // ItemCode
    
            // Check if the course exists in the updated courses array
            if (isset($updatedCourses[$itemCode])) {
                // Write the updated course row
                fputcsv($fpTemp, $updatedCourses[$itemCode]);
                unset($updatedCourses[$itemCode]); // Remove it after writing to avoid duplication
            } else {
                // Write the existing row as is
                fputcsv($fpTemp, $row);
            }
        }
        fclose($fpOriginal);
    }                                                                                                                           
    
    // Append any remaining new courses that were not in the original file
    foreach ($updatedCourses as $newCourse) {
        fputcsv($fpTemp, $newCourse);
    }

    fclose($fpTemp);

    // Replace the original file with the updated temporary file
    rename($tempFilePath, $coursesPath);
}

header('Location: feed-create.php');
?>
<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>