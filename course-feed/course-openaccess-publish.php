<?php
opcache_reset();
// Include required files that contain getCourse and getCoursesClassesUpcoming
require_once '../inc/lsapp.php'; // Ensure this contains getCoursesClassesUpcoming()
$csvFile = fopen('../data/courses.csv', 'r');
if (!$csvFile) {
    die("Failed to open courses CSV.");
}

$header = fgetcsv($csvFile); // Skip header row
$allCourseIds = []; // Track all course IDs for later cleanup

$accessCodeJson = '../data/open-access-code.json';
$accessCodeData = file_exists($accessCodeJson) ? json_decode(file_get_contents($accessCodeJson), true) : [];
$expectedCode = $accessCodeData[0]['code'] ?? '';

while (($course = fgetcsv($csvFile)) !== false) {
    $allCourseIds[] = $course[0]; // Track all course IDs for later cleanup
    if (isset($course[57]) && strtolower(trim($course[57])) === 'true' || strtolower(trim($course[57])) === 'on') {
        $courseid = $course[0]; // Assuming course ID is in index 0

        // Fetch course details
        $course = getCourse($courseid);
        if (!$course) {
            die("Course not found.");
        }

        // Extract required course details
        $title = $course[2]; // CourseName
        $description = $course[16]; // CourseDescription
        $preWork = $course[8] ?? '';
        $courseShort = str_replace(' ', '-', strtolower($course[3])); // CourseShort

        // Fetch all upcoming classes
        $upcomingClasses = getCoursesClassesUpcoming($courseid);
        $nextOffering = "<div class='alert alert-warning'>No upcoming offerings.</div>";

        if (!empty($upcomingClasses)) {
            $nextOffering = "<table class='table table-bordered table-striped'>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Enrolment</th>
                                        <th>Webinar Link</th>
                                    </tr>
                                </thead>
                                <tbody>";
        
            $today = date('Y-m-d'); // Get today's date
        
            foreach ($upcomingClasses as $class) {
                if($class[1] === 'Active') {
                    $startDate = $class[8]; // StartDate
                    $startTime = $class[54]; // StartTime
                    $endTime = $class[55]; // EndTime
                    $webinarLink = $class[15]; // Webinar link
                    $currentEnrolment = $class[18]; // current enrolment number
                    $maxEnrolment = $class[12]; // maximum enrolment number
            
                    // Format the date
                    $dateObj = new DateTime($startDate);
                    $formattedDate = $dateObj->format('l, M j Y');
            
                    // Only show webinar link if the event is happening today
                    $webinarCell = "";
                    if ($startDate === $today) {
                        // Determine the help link for the webinar
                        $help = "";
                        if (strpos($webinarLink, 'teams.microsoft.com') !== false) {
                            $help = '<a href="https://aka.ms/JoinTeamsMeeting?omkt=en-US" target="_blank" rel="noopener">Need help?</a>';
                        } elseif (strpos($webinarLink, 'zoom.us') !== false) {
                            $help = ''; // Zoom help TBD
                        }
            
                        $webinarCell = "<a class='btn btn-primary' href='{$webinarLink}' target='_blank' rel='noopener'>Join</a> $help";
                    } else {
                        $webinarCell = "<span class='text-muted'>Available on the day</span>";
                    }
            
                    // Add class row
                    $nextOffering .= "<tr>
                                        <td>{$formattedDate}</td>
                                        <td>{$startTime} - {$endTime}</td>
                                        <td>{$currentEnrolment}/{$maxEnrolment}</td>
                                        <td>{$webinarCell}</td>
                                    </tr>";
                }
            }
        
            $nextOffering .= "</tbody></table>";
        }

        // Directory where the new index.php will be placed
        $directory = 'E:/WebSites/NonSSOLearning/openaccess';

        // Check if directory exists, if not, create it
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Define file paths
        $headerFile = '../templates/openaccess-header.php';
        $footerFile = '../templates/openaccess-footer.php';
        $indexFile = $directory . '/' . $courseShort . '.php';

        // Read header and footer
        ob_start();
        if (file_exists($headerFile)) {
            include $headerFile;
        } else {
            echo "<html><head><title>$title</title></head><body>";
        }
        $headerContent = ob_get_clean();

        ob_start();
        if (file_exists($footerFile)) {
            include $footerFile;
        } else {
            echo "</body></html>";
        }
        $footerContent = ob_get_clean();

        // Content for the index.php file (including the access code check)
        $pageContent = "<?php 
        \$code = \$_GET['accesscode'] ?? ''; 
        if (\$code !== '" . addslashes($expectedCode) . "') {
            die('Sorry, you do not have access.');
        } 
        ?>" . PHP_EOL;

        $pageContent .= $headerContent . "
        <h1>$title</h1>
        <p>$description</p>
        <div><a class=\"btn btn-lg btn-secondary\" href=\"$preWork\" target=\"_blank\" rel=\"noopener\">Pre-work Link</a></div>
        <p>$nextOffering</p>
        " . $footerContent;

        // Write the index.php file
        file_put_contents($indexFile, $pageContent);
    }
}

// Cleanup unpublished courses
$existingFiles = glob($directory . '/*.php');
foreach ($existingFiles as $file) {
    $filename = basename($file, '.php');
    $match = false;
    foreach ($allCourseIds as $id) {
        $courseData = getCourse($id);
        if ($courseData && isset($courseData[3])) {
            $short = str_replace(' ', '-', strtolower($courseData[3]));
            if ($short === $filename) {
                if (isset($courseData[57]) && (strtolower(trim($courseData[57])) === 'true' || strtolower(trim($courseData[57])) === 'on')) {
                    $match = true;
                    break;
                }
            }
        }
    }
    if (!$match) {
        unlink($file);
    }
}

fclose($csvFile);
// exit;
header('Location: index.php?message=Success');
