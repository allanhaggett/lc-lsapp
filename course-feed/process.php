<?php 
opcache_reset();

$cats = '';
$finalCols = ['Course Code', 'Course Name', 'Course Description', 'Delivery Method', 'Category', 'Learner Group', 
              'Duration', 'Available Classes', 'Link to ELM Search', 'Course Last Modified', 
              'Course Owner Org', 'Course ID', 'Keywords', 'Group', 'Audience', 'Topic'];

// Process keywords CSV
$keywordsFile = "data/GBC_ATWORK_CATALOG_KEYWORDS.csv";
$keywords = processCsvFile($keywordsFile);
$keys = mergeKeywords($keywords);

// Process catalog CSV
$catalogFile = "data/GBC_LEARNINGHUB_SYNC2.csv";
$courses = processCsvFile($catalogFile);
$newCourses = mergeCourses($courses, $keys);

// Write final course data to CSV
writeCsv('data/courses.csv', $finalCols, $newCourses);

// Redirect
header('Location: lhub-course-sync.php');

// Functions
function processCsvFile($filename) {
    if (($handle = fopen($filename, "r")) === false) return [];

    $csvData = [];
    $columnNames = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $csvData[] = array_combine($columnNames, $row);
    }
    fclose($handle);
    return $csvData;
}

function mergeKeywords($keywords) {
    $keys = [];
    $lastCode = '';
    $k = $partnerKey = '';

    foreach ($keywords as $keyword) {
        $code = $keyword['Course Code'];
        $key = $keyword['Keyword'];
        if ($lastCode && $code != $lastCode) {
            $keys[] = [$lastCode, trim($k, ', '), $partnerKey];
            $k = $partnerKey = '';
        }
        if ($keyword['Keyword Type ID'] == 1039) $partnerKey = $key;
        $k .= $key . ', ';
        $lastCode = $code;
    }
    return $keys;
}

function mergeCourses($courses, $keys) {
    $newCourses = [];
    $lastCode = '';
    $categories = $kwords = $learningPartner = $audience = $group = $topic = '';

    foreach ($courses as $course) {
        if (!in_array($course['Learner Group'], ['All Government of British Columbia Learners', 'Excluded Managers', 'Ministries - All'])) {
            continue;
        }

        $currentCode = $course['Course Code'];
        if ($lastCode && $currentCode != $lastCode) {
            if ($learningPartner && $audience && $group && $topic) {  // Only add if learningPartner is set
                $newCourses[] = buildCourseArray($course, $categories, $learningPartner, $kwords, $group, $audience, $topic);
            } else {
                //echo "Warning: Course {$lastCode} is missing a learning partner, audience, group, or topic.\n";
            }
            $categories = $kwords = $learningPartner = $audience = $group = $topic = '';
        }

        // Categories, Keywords, Group, Audience, and Topic aggregation based on Short Name
        if (strlen($course['Category']) > 0) {
            switch ($course['Short Name']) {
                case 'Audience':
                    $audience = $course['Category'];
                    break;
                case 'Group':
                    $group = $course['Category'];
                    break;
                case 'Topic':
                    $topic = $course['Category'];
                    break;
            }
            $categories .= $course['Category'] . ', ';
            $categories = rtrim($categories, ',');
        }

        foreach ($keys as $k) {
            if ($k[0] == $currentCode) {
                $kwords .= $k[1] . ',';
                $learningPartner = !empty($k[2]) ? mapPartnerCode($k[2]) : '';  // Only set if $k[2] is not empty
            }
        }

        $lastCode = $currentCode;
    }
    return $newCourses;
}

function mapPartnerCode($code) {
    $partners = [
        'EMCR' => 'Emergency Management and Climate Readiness',
        'Priorities & Innovation' => 'Leadership, Engagement and Priority Initiatives',
        'CIRMO' => 'Corporate Information and Records Management Office',
        'DWCS' => 'Digital Workplace and Collaboration Services Branch',
    ];
    return $partners[$code] ?? $code;
}

function buildCourseArray($course, $categories, $learningPartner, $keywords, $group, $audience, $topic) {
    return [
        $course['Course Code'],
        htmlspecialchars($course['Course Name'], ENT_QUOTES, 'UTF-8'),
        trim_all($course['Course Description']),
        convertDeliveryMethod($course['Delivery Method']),
        rtrim($categories, ', '),
        $course['Learner Group'],
        'Not Listed', // Duration placeholder
        $course['Available Classes'],
        $course['Link to ELM Search'],
        $course['Course Last Modified'],
        $learningPartner,
        $course['Course ID'],
        rtrim($keywords, ', '),
        $group,
        $audience,
        $topic
    ];
}

function writeCsv($filename, $headers, $data) {
    $fp = fopen($filename, 'w');
    fputcsv($fp, $headers);
    foreach ($data as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}

function trim_all($str, $what = null, $with = ' ') {
    $what = $what ?? "\\x00-\\x20";
    return trim(preg_replace("/[" . $what . "]+/", $with, $str), $what);
}

function convertDeliveryMethod($method) {
    return str_replace(
        ['Moodle', 'Virtual', 'Self-Directed', 'Scheduled Learning Activities', 'Self-Paced Learning Activities'],
        'eLearning',
        $method
    );
}
