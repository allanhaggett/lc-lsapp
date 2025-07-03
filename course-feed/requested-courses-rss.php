<?php
opcache_reset();

// Read the courses CSV file
if (($handle = fopen("../data/courses.csv", "r")) !== false) {
    $csvs = [];
    while (($row = fgetcsv($handle)) !== false) {
        if (!empty(array_filter($row))) {  // Skip empty rows
            $csvs[] = $row;
        }
    }
    fclose($handle);

    // Ensure the file is not empty before processing
    if (!empty($csvs)) {
        // Map column names to CSV values
        $datas = [];
        $column_names = $csvs[0];
        foreach ($csvs as $key => $csv) {
            if ($key === 0) continue;  // Skip header row
            foreach ($column_names as $column_key => $column_name) {
                $datas[$key - 1][$column_name] = $csv[$column_key] ?? '';
            }
        }
    } else {
        echo "CSV file is empty or not properly formatted.";
        exit;
    }
} else {
    echo "Unable to open CSV file.";
    exit;
}

// Start building the RSS feed
$rss = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
$rss .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
$rss .= '<channel>' . "\n";
$rss .= '<title>Review Courses for LearningHUB</title>' . "\n";
$rss .= '<link>https://corporatelearning.gww.gov.bc.ca/learninghub/</link>' . "\n";
$rss .= '<description>New course requests from BC Government Learning Partners awaiting review</description>' . "\n";
$rss .= '<language>en-us</language>' . "\n";
$rss .= '<lastBuildDate>' . date(DATE_RSS) . '</lastBuildDate>' . "\n";
$rss .= '<atom:link href="https://learn.bcpublicservice.gov.bc.ca/learning-hub/requested-courses.xml" rel="self" type="application/rss+xml" />' . "\n";

// Filter and add requested courses to the RSS feed
foreach ($datas as $course) {
    // Only include courses with status "Request" or "Requested"
    if ($course['Status'] == 'Request' || $course['Status'] == 'Requested') {
        $title = htmlspecialchars($course['CourseName'] ?? '');
        $description = htmlspecialchars($course['CourseDescription'] ?? '');
        $partner = htmlspecialchars($course['LearningHubPartner'] ?? '');
        $method = htmlspecialchars($course['Method'] ?? '');
        $platform = htmlspecialchars($course['Platform'] ?? '');
        $requestedBy = htmlspecialchars($course['RequestedBy'] ?? '');
        $requestedDate = $course['Requested'] ?? '';
        
        // Format the requested date for RSS
        $pubDate = '';
        if (!empty($requestedDate)) {
            try {
                $dateObj = new DateTime($requestedDate);
                $pubDate = $dateObj->format(DATE_RSS);
            } catch (Exception $e) {
                $pubDate = date(DATE_RSS);
            }
        } else {
            $pubDate = date(DATE_RSS);
        }
        
        // Build the course URL (using the course ID)
        $courseUrl = 'https://gww.bcpublicservice.gov.bc.ca/lsapp/course.php?courseid=' . urlencode($course['CourseID']);
        
        // Create a more detailed description including metadata
        $fullDescription = $description;
        if (!empty($partner)) {
            $fullDescription .= "\n\nLearning Partner: " . $partner;
        }
        if (!empty($method)) {
            $fullDescription .= "\nDelivery Method: " . $method;
        }
        if (!empty($platform)) {
            $fullDescription .= "\nPlatform: " . $platform;
        }
        if (!empty($requestedBy)) {
            $fullDescription .= "\nRequested By: " . $requestedBy;
        }
        
        // Add the item to the RSS feed
        $rss .= '<item>' . "\n";
        $rss .= '<title>' . $title . '</title>' . "\n";
        $rss .= '<link>' . $courseUrl . '</link>' . "\n";
        $rss .= '<description><![CDATA[' . nl2br($fullDescription) . ']]></description>' . "\n";
        $rss .= '<guid isPermaLink="true">' . $courseUrl . '</guid>' . "\n";
        $rss .= '<pubDate>' . $pubDate . '</pubDate>' . "\n";
        
        // Add categories/tags
        if (!empty($course['Topics'])) {
            $rss .= '<category>' . htmlspecialchars($course['Topics']) . '</category>' . "\n";
        }
        if (!empty($course['Audience'])) {
            $rss .= '<category>' . htmlspecialchars($course['Audience']) . '</category>' . "\n";
        }
        
        $rss .= '</item>' . "\n";
    }
}

$rss .= '</channel>' . "\n";
$rss .= '</rss>';

// Write the RSS feed to a file
$rssFilename = 'data/learninghub-courses-for-review.xml';
file_put_contents($rssFilename, $rss);

// Copy to the web-accessible location
$targetFile = 'E:/WebSites/NonSSOLearning/learning-hub/learninghub-courses-for-review.xml';
if (!copy($rssFilename, $targetFile)) {
    echo 'Failed to copy ' . $rssFilename . ' to ' . $targetFile;
    exit;
}

// Redirect to the course changes RSS feed generation
header('Location: course-changes-rss.php');