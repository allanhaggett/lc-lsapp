<?php
opcache_reset();

// Set proper RSS headers
header('Content-Type: application/rss+xml; charset=UTF-8');

// Load courses data
if (($handle = fopen("data/courses.csv", "r")) !== false) {
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
        echo "<?xml version='1.0' encoding='UTF-8'?><error>CSV file is empty or not properly formatted.</error>";
        exit;
    }
} else {
    echo "<?xml version='1.0' encoding='UTF-8'?><error>Unable to open CSV file.</error>";
    exit;
}

// Filter for requested courses only
$requestedCourses = array_filter($datas, function($course) {
    return $course['Status'] === 'Requested';
});

// Sort by requested date (newest first)
usort($requestedCourses, function($a, $b) {
    $dateA = strtotime($a['Requested'] ?? '1970-01-01');
    $dateB = strtotime($b['Requested'] ?? '1970-01-01');
    return $dateB - $dateA; // Descending order (newest first)
});

// Create RSS XML structure
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"></rss>');
$channel = $xml->addChild('channel');
$channel->addChild('title', 'New Course Requests - BC Gov Learning Hub');
$channel->addChild('link', 'https://learningcentre.gww.gov.bc.ca/learninghub/');
$channel->addChild('description', 'RSS feed of new course requests submitted to the BC Gov Learning Hub');
$channel->addChild('language', 'en-us');
$channel->addChild('lastBuildDate', date('D, d M Y H:i:s O'));
$channel->addChild('generator', 'LSApp Course Request RSS Generator');

// Add RSS items for each requested course
foreach ($requestedCourses as $course) {
    $item = $channel->addChild('item');
    
    // Basic RSS item fields
    $courseName = htmlspecialchars($course['CourseName'] ?? 'Untitled Course', ENT_XML1);
    $item->addChild('title', $courseName);
    
    // Link back to the course in LSApp
    $courseLink = 'https://learningcentre.gww.gov.bc.ca/lsapp/course.php?courseid=' . urlencode($course['CourseID'] ?? '');
    $item->addChild('link', $courseLink);
    
    // Unique identifier
    $guid = $item->addChild('guid', $courseLink);
    $guid->addAttribute('isPermaLink', 'true');
    
    // Publication date (when requested)
    if (!empty($course['Requested'])) {
        $pubDate = date('D, d M Y H:i:s O', strtotime($course['Requested']));
        $item->addChild('pubDate', $pubDate);
    }
    
    // Description with course details
    $description = '';
    if (!empty($course['CourseDescription'])) {
        $description .= '<p><strong>Description:</strong> ' . htmlspecialchars($course['CourseDescription'], ENT_XML1) . '</p>';
    }
    if (!empty($course['RequestedBy'])) {
        $description .= '<p><strong>Requested by:</strong> ' . htmlspecialchars($course['RequestedBy'], ENT_XML1) . '</p>';
    }
    if (!empty($course['LearningHubPartner'])) {
        $description .= '<p><strong>Learning Partner:</strong> ' . htmlspecialchars($course['LearningHubPartner'], ENT_XML1) . '</p>';
    }
    if (!empty($course['Method'])) {
        $description .= '<p><strong>Delivery Method:</strong> ' . htmlspecialchars($course['Method'], ENT_XML1) . '</p>';
    }
    if (!empty($course['Category'])) {
        $description .= '<p><strong>Category:</strong> ' . htmlspecialchars($course['Category'], ENT_XML1) . '</p>';
    }
    if (!empty($course['Keywords'])) {
        $description .= '<p><strong>Keywords:</strong> ' . htmlspecialchars($course['Keywords'], ENT_XML1) . '</p>';
    }
    
    $item->addChild('description', $description);
    
    // Additional fields
    if (!empty($course['RequestedBy'])) {
        $item->addChild('author', htmlspecialchars($course['RequestedBy'], ENT_XML1));
    }
    
    if (!empty($course['Category'])) {
        $categories = explode(',', $course['Category']);
        foreach ($categories as $cat) {
            $cat = trim($cat);
            if (!empty($cat)) {
                $item->addChild('category', htmlspecialchars($cat, ENT_XML1));
            }
        }
    }
    
    // Custom fields for additional metadata
    if (!empty($course['CourseID'])) {
        $item->addChild('courseId', htmlspecialchars($course['CourseID'], ENT_XML1));
    }
    if (!empty($course['LearningHubPartner'])) {
        $item->addChild('learningPartner', htmlspecialchars($course['LearningHubPartner'], ENT_XML1));
    }
    if (!empty($course['Method'])) {
        $item->addChild('deliveryMethod', htmlspecialchars($course['Method'], ENT_XML1));
    }
}

// Output the RSS XML
echo $xml->asXML();