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

$json = [
    "version" => "https://jsonfeed.org/version/1",
    "title" => "BC Gov Corporate Learning Courses",
    "home_page_url" => "https://learningcentre.gww.gov.bc.ca/learninghub/",
    "feed_url" => "https://learn.bcpublicservice.gov.bc.ca/learning-hub/bcps-corporate-learning-courses.json",
    "items" => []
];

foreach ($datas as $course) {
    $description = preg_replace("/\r|\n/", "", htmlentities($course['CourseDescription'] ?? ''));
    $desc = iconv(mb_detect_encoding($description, mb_detect_order(), true), "UTF-8", $description);
    $createdDate = date("Y-m-d\TH:i:s", strtotime(str_replace('  ', ' ', $course['Requested'] ?? '')));
    $modifiedDate = date("Y-m-d\TH:i:s", strtotime(str_replace('  ', ' ', $course['Modified'] ?? '')));

    if ($course['Status'] == 'Active' && !empty($course['LearningHubPartner'])  && $course['HUBInclude'] > 0) {
        $json['items'][] = [
            "id" => $course['ItemCode'] ?? '',
            "title" => $course['CourseName'] ?? '',
            "summary" => $desc,
            "content_text" => $course['CourseName'] ?? '',
            "delivery_method" => $course['Method'] ?? '',
            "_course_id" => $course['CourseID'] ?? '',
            "_keywords" => $course['Keywords'] ?? '',
            "_audience" => $course['Audience'] ?? '',
            "_topic" => $course['Topics'] ?? '',
            "_slug" => $course['CourseNameSlug'] ?? '',
            "_learning_partner" => $course['LearningHubPartner'] ?? '',
            "_platform" => $course['Platform'] ?? '',
            "author" => $course['LearningHubPartner'] ?? '',
            "date_published" => $createdDate,
            "date_modified" => $modifiedDate,
            "tags" => rtrim(trim($course['Category'] ?? ''), ','),
            "url" => "https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_CRS_DTL_FL.GBL?Page=LM_CRS_DTL_FL&Action=U&ForceSearch=Y&LM_CI_ID=" . ($course['CourseID'] ?? '')
        ];
    }
}

$jsonOutput = json_encode($json, JSON_PRETTY_PRINT);
$jsonFilename = 'data/bcps-corporate-learning-courses.json';
file_put_contents($jsonFilename, $jsonOutput);

// $newfile = 'E:/WebSites/NonSSOLearning/learning-hub/bcps-corporate-learning-courses.json';
// if (!copy($jsonFilename, $newfile)) {
//     echo 'Failed to copy ' . $jsonFilename . '... contact Allan';
//     exit;
// }

// header('Location: ' . $jsonFilename);
header('Location: index.php?message=Success');
// header('Location: rss2-feed-create.php');
