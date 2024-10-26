<?php
opcache_reset();

if (($handle = fopen("data/courses.csv", "r")) !== false) {
    $csvs = [];
    while (($row = fgetcsv($handle)) !== false) {
        if (!empty(array_filter($row))) {  // Skip empty rows
            $csvs[] = $row;
        }
    }
    fclose($handle);

    // Map column names to CSV values
    $datas = [];
    $column_names = $csvs[0];
    foreach ($csvs as $key => $csv) {
        if ($key === 0) continue;  // Skip header row
        foreach ($column_names as $column_key => $column_name) {
            $datas[$key - 1][$column_name] = $csv[$column_key] ?? '';
        }
    }
}

$json = [
    "version" => "https://jsonfeed.org/version/1",
    "title" => "BC Gov Corporate Learning Courses",
    "home_page_url" => "https://learningcentre.gww.gov.bc.ca/learninghub/",
    "feed_url" => "https://learn.bcpublicservice.gov.bc.ca/learning-hub/learning-partner-courses.json",
    "items" => []
];

foreach ($datas as $course) {
    $description = preg_replace("/\r|\n/", "", htmlentities($course['Course Description'] ?? ''));
    $desc = iconv(mb_detect_encoding($description, mb_detect_order(), true), "UTF-8", $description);
    $formattedDate = date("Y-m-d\TH:i:s", strtotime(str_replace('  ', ' ', $course['Course Last Modified'] ?? '')));

    $json['items'][] = [
        "id" => $course['Course Code'] ?? '',
        "title" => $course['Course Name'] ?? '',
        "summary" => $desc,
        "content_text" => $course['Course Name'] ?? '',
        "content_html" => "<div>{$course['Course Name']}</div>",
        "delivery_method" => $course['Delivery Method'] ?? '',
        "_available_classes" => $course['Available Classes'] ?? '',
        "_course_id" => $course['Course ID'] ?? '',
        "_keywords" => $course['Keywords'] ?? '',
        "_audience" => $course['Audience'] ?? '',
        "_topic" => $course['Topic'] ?? '',
        "_learning_partner" => $course['Course Owner Org'] ?? '',
        "_external_system" => "PSA Learning System",
        "author" => $course['Course Owner Org'] ?? '',
        "date_published" => "2020-05-13T14:00:00",
        "date_modified" => $formattedDate,
        "tags" => rtrim(trim($course['Category'] ?? ''), ','),
        "url" => "https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_CRS_DTL_FL.GBL?Page=LM_CRS_DTL_FL&Action=U&ForceSearch=Y&LM_CI_ID=" . ($course['Course ID'] ?? '')
    ];
}

$jsonOutput = json_encode($json, JSON_PRETTY_PRINT);
$jsonFilename = 'data/learning-partner-courses.json';
file_put_contents($jsonFilename, $jsonOutput);

// $newfile = 'E:/WebSites/NonSSOLearning/learning-hub/learning-partner-courses.json';
// if (!copy($jsonFilename, $newfile)) {
//     echo 'Failed to copy ' . $jsonFilename . '... contact Allan';
//     exit;
// }

// Redirect to success page
// header('Location: index.php?message=Success');
header('Location: ' . $jsonFilename);
