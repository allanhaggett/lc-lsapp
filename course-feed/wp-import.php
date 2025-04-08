<?php

$xmlFile = __DIR__ . '/wordpress-export.xml';
$csvFile = __DIR__ . '/courses.csv';
$logFile = __DIR__ . '/added-courses-log-' . date('Ymd_His') . '.csv';

// Load the XML file
if (!file_exists($xmlFile)) {
    die("WXR XML file not found.\n");
}

$xml = simplexml_load_file($xmlFile, 'SimpleXMLElement', LIBXML_NOCDATA);
$xml->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
$xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
$xml->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
$xml->registerXPathNamespace('excerpt', 'http://wordpress.org/export/1.2/excerpt/');

$items = $xml->xpath('//channel/item');

// Load existing CSV
$existingCourses = [];
$headers = [];
if (($handle = fopen($csvFile, 'r')) !== false) {
    $headers = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $record = array_combine($headers, $row);
        $existingCourses[$record['CourseName']] = $record;
    }
    fclose($handle);
} else {
    die("Could not read CSV file.\n");
}

// Required CSV fields
$requiredFields = ['CourseName', 'CourseNameSlug', 'CourseDescription', 'Topics', 'Method', 'Audience', 'LearningHubPartner', 'Platform'];

// Prepare new rows
$newRows = [];

foreach ($items as $item) {
    $postType = (string)$item->children('wp', true)->post_type;
    if ($postType !== 'course') continue;

    $title = trim((string)$item->title);
    $content = trim((string)$item->children('content', true)->encoded);

    if (isset($existingCourses[$title])) continue; // already in CSV

    // Prepare taxonomy values
    $taxonomies = [
        'topic'            => [],
        'delivery_method'  => [],
        'audience'         => [],
        'external_system'  => [],
        'learning_partner' => [],
    ];

    foreach ($item->category as $category) {
        $domain = (string)$category['domain'];
        $term = (string)$category;
        if (isset($taxonomies[$domain])) {
            $taxonomies[$domain][] = $term;
        }
    }

    $row = array_fill_keys($headers, ''); // initialize all fields with blank

    $row['CourseName']         = $title;
    $row['CourseNameSlug']     = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $row['CourseDescription']  = strip_tags($content);
    $row['Topics']             = implode(', ', $taxonomies['topic']);
    $row['Method']             = implode(', ', $taxonomies['delivery_method']);
    $row['Audience']           = implode(', ', $taxonomies['audience']);
    $row['LearningHubPartner'] = implode(', ', $taxonomies['learning_partner']);
    $row['Platform']           = implode(', ', $taxonomies['external_system']);

    // Append to list
    $existingCourses[$title] = $row;
    $newRows[] = $row;
}

// Write updated CSV
if (!empty($newRows)) {
    $output = fopen($csvFile, 'w');
    fputcsv($output, $headers);
    foreach ($existingCourses as $record) {
        $line = [];
        foreach ($headers as $header) {
            $line[] = $record[$header] ?? '';
        }
        fputcsv($output, $line);
    }
    fclose($output);

    // Write log
    $log = fopen($logFile, 'w');
    fputcsv($log, $headers);
    foreach ($newRows as $row) {
        $line = [];
        foreach ($headers as $header) {
            $line[] = $row[$header] ?? '';
        }
        fputcsv($log, $line);
    }
    fclose($log);

    echo count($newRows) . " new course(s) added.\nLog written to: $logFile\n";
} else {
    echo "No new courses found.\n";
}