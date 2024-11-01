<?php
opcache_reset();

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

// Define the XML RSS structure
$xml = new SimpleXMLElement('<rss version="2.0"/>');
$channel = $xml->addChild('channel');
$channel->addChild('title', 'BC Gov Corporate Learning Courses');
$channel->addChild('link', 'https://learningcentre.gww.gov.bc.ca/learninghub/');
$channel->addChild('description', 'List of BC Gov corporate learning courses');
$channel->addChild('language', 'en-us');

foreach ($datas as $course) {
    $description = htmlentities($course['CourseDescription'] ?? '', ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $desc = mb_substr($description, 0, 300); // Limit to 300 characters
    $formattedDate = date("Y-m-d\TH:i:s", strtotime(str_replace('  ', ' ', $course['Modified'] ?? '')));

    if ($course['Status'] == 'Active' && !empty($course['LearningHubPartner']) ) { //&& $course['HUBInclude'] > 0
        $item = $channel->addChild('item');
        $item->addChild('title', $course['CourseName'] ?? '');
        $item->addChild('link', "https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_CRS_DTL_FL.GBL?Page=LM_CRS_DTL_FL&Action=U&ForceSearch=Y&LM_CI_ID=" . ($course['CourseID'] ?? ''));
        $item->addChild('guid', $course['ItemCode'] ?? '');
        $item->addChild('pubDate', date("D, d M Y H:i:s O", strtotime($formattedDate)));
        $item->addChild('description', $desc);
        
        // Optional custom fields for additional data
        $item->addChild('category', rtrim(trim($course['Category'] ?? ''), ','));
        $item->addChild('author', $course['LearningHubPartner'] ?? '');

        // Custom elements (use namespaces to avoid RSS validation issues)
        $namespaces = [
            '_course_id' => $course['CourseID'] ?? '',
            '_keywords' => $course['Keywords'] ?? '',
            '_audience' => $course['Audience'] ?? '',
            '_topic' => $course['Topics'] ?? '',
            '_learning_partner' => $course['LearningHubPartner'] ?? '',
            '_external_system' => $course['ExternalSystem'] ?? ''
        ];
        foreach ($namespaces as $key => $value) {
            if (!empty($value)) {
                $item->addChild($key, $value);
            }
        }
    }
}

// Save the RSS feed as an XML file
$rssFilename = 'data/corporate-learning-courses.xml';
file_put_contents($rssFilename, $xml->asXML());

// Attempt to copy the file to a new location
$newfile = 'E:/WebSites/NonSSOLearning/learning-hub/learning-partner-courses.xml';
if (!copy($rssFilename, $newfile)) {
    echo "Failed to copy $rssFilename... contact Allan";
    exit;
}

// Redirect or provide success feedback
//header('Location: index.php?message=Success');
